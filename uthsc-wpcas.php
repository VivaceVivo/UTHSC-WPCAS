<?php
/**
 * Plugin Name: Customer 360 - WPCAS
 * Plugin URI: https://github.com/VivaceVivo/UTHSC-WPCAS
 * Description: A plugin that uses phpCAS to integrate CAS with WordPress.
 * Author: Patrick Trapp - CGI... based on the works by George Spake - UTHSC
 * Version: 0.3.0
 * Author URI: http://cgi.com/
 * License: GPLv3
*/

/*
A plugin that uses phpCAS to integrate CAS with WordPress.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//To Do:
//Lockdown option to restrict users who aren't authenticated
//Update user info if cas response doesn't match user account
// (Restrict access to users who already have WordPress accounts (WP Accounts must be entered manually before users can Authenticate with CAS) )


//Checks if the plugin class has already been defined. If not, it defines it here.
//This is to avoid class name conflicts within WordPress and plugins.


if ( !class_exists('UTHSCWPCAS') ) {

	class UTHSCWPCAS {

		public function __construct() {

			add_action('login_init',array($this, 'bypass_login'));
			
			//Hook into WordPress authentication system
			$this -> wp_cas_authentication_hooks();

			//Register settings
			add_action('admin_init', array($this, 'register_wpcas_settings'));

			//Add options to admin menu
			if (is_admin()) {
				include_once('admin/wpsso-about.php');
				include_once('admin/wpsso-settings.php');
				include_once('admin/wpsso-test.php');
				add_action('admin_menu', array($this, 'add_options_pages'));
			}

			//activation hooks
			if (isset($_GET['activate']) and $_GET['activate'] == 'true') {
				add_action('init', array(&$this, 'activate'));
			}
			
			if ( get_option( 'uthsc_wpcas_host' ) ) {
				error_log("initializing SSO: " . get_option( 'uthsc_wpcas_host' ), 0);
				//Initialize phpCAS
				$this->initialize_phpCAS();
			}

			//Get wpcas options
			require_once('admin/wpsso-options.php');
		}

		protected function wp_cas_authentication_hooks(){
			//add_action('init', array('UTHSCWPCAS', 'lock_down_check'));
			add_filter('authenticate', array(&$this, 'authenticate'), 10, 3);
			add_filter('wp_signon', array(&$this, 'authenticate'),10,3);

			add_action('wp_logout', array(&$this, 'logout'));
			add_action('lost_password', array('UTHSCWPCAS', 'disable_function'));
			add_action('retrieve_password', array('UTHSCWPCAS', 'disable_function'));
			add_action('password_reset', array('UTHSCWPCAS', 'disable_function'));
			add_filter('show_password_fields', array(&$this, 'show_password_fields'));
			//add_action('check_passwords', array('UTHSCWPCAS', 'check_passwords'), 10, 3);
			
			add_filter('login_url', array(&$this, 'wpcas_login_url'),10,3);
			add_filter( 'register_url', array(&$this, 'wpcas_register_url'),10,3 );
		}

		// public static function check_passwords() {
		// 	// nothing to do
		// }

		//Register settings in admin/wpsso-options.php
		public function register_wpcas_settings() {
			$wpcas_options = new WPSSO_Options;

			foreach ( $wpcas_options->wpsso_settings() as $group => $options) {
				foreach ($options as $option => $default){
					register_setting($group, $option);
				}
			}
		}

		//Update settings in admin/wpsso-options.php
		public static function activate() {
			$wpcas_options = new WPSSO_Options;
			
			foreach ( $wpcas_options->wpsso_settings() as $group => $options) {
				foreach ($options as $option => $default){
					// error_log($option . " -> " . $default, 0);
					update_option($option, $default);
				}
			}       
		}

		//Unregister settings in lib/wpcas-options.php
		public static function deactivate() {
			$wpcas_options = new WPSSO_Options;
			
			foreach ( $wpcas_options->wpsso_settings() as $group => $options) {
				foreach ($options as $option => $default){
					update_option($option, '');
					unregister_setting($group, $option);
				}
			}
		}

		//Delete settings in lib/wpcas-options.php
		public static function uninstall() {
			$wpcas_options = new WPSSO_Options;
			
			foreach ( $wpcas_options->wpsso_settings() as $group => $options) {
				foreach ($options as $option => $default){
					delete_option($option);
				}
			}
		}

		public function add_options_pages() {
			$icon = plugin_dir_url( __FILE__ ).'img/cas-logo.png';

			add_menu_page('UTHSC WP CAS', 'UTHSC WP CAS', 'administrator', 'uthsc-wpcas-settings', 'uthsc_wpcas_preferences', $icon, '98.9');
			add_submenu_page('uthsc-wpcas-settings', 'CAS Test', 'CAS Test', 'administrator', 'uthsc-wpcas-test', 'uthsc_wpcas_test');
			add_submenu_page('uthsc-wpcas-settings', 'About', 'About', 'administrator', 'wpsso_about', 'wpsso_about');		
		}

		public function bypass_login(){
			error_log("bypass_login... ", 0);
			if ( ! phpCAS::isAuthenticated() ) {
				
				if (isset($_GET['redirect_to']) && $_GET['redirect_to']) {
					$redirect = wp_login_url( $_GET['redirect_to'] );
				} else {
					$redirect = wp_login_url(); //TODO: Add default redirect?
				}
				header( 'Location: ' . $redirect );
			}
		}

		public function wpcas_login_url($redirect = '', $force_reauth = false) {

			$pattern = get_option('uthsc_wpcas_native_login_url_pattern');
			if($pattern){
				if( strpos ($_SERVER["REQUEST_URI"], $pattern ) ) {	
						
					error_log("login URL: " . site_url('wp-login.php', 'login'), 0);
					
					return site_url('wp-login.php', 'login'). "?doNativeLogin=true";
				}
			}
			
			$login_url = site_url('wp-login.php', 'login');
			
			if ( !empty($redirect) ) {

				if (get_permalink()){
					$redirect = get_permalink();
				} else {
				 	$redirect = $login_url;
				}
				$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);

			}

			return 'https://'. get_option('uthsc_wpcas_host') .":" . get_option('uthsc_wpcas_port')  . get_option('uthsc_wpcas_context') . '/login?service='. $login_url;

		}

		public function wpcas_register_url($redirect = '', $force_reauth = false) {
			$redirect_url = site_url('wp-login.php', 'login');
			
			if ( !empty($redirect) ) {

				if (get_permalink()){
					$redirect = get_permalink();
				} else {
				 	$redirect = $redirect_url;
				}
					$redirect_url = add_query_arg('redirect_to', urlencode($redirect), $redirect_url);

			}

			return 'https://'. get_option('uthsc_wpcas_host') .":" . get_option('uthsc_wpcas_port')  . get_option('uthsc_wpcas_context') . '/register?service='. $redirect_url;

		}

		protected function initialize_phpCAS() {

			//This comes from phpCAS's authpage.php example but instead of using the options from config.php we get the wordpress options that are set in the plugin admin section.
			//If you want to test CAS to see if it's working, you can use the authpage.php included in the plugin directory.

			// Load the settings from the central config file
			//require_once 'config.php';
			
			// Load the CAS lib
			require_once 'phpCAS-1.3-stable/CAS.php';
			
			// Initialize phpCAS
			phpCAS::client(SAML_VERSION_1_1, get_option('uthsc_wpcas_host'),intval(get_option('uthsc_wpcas_port')), get_option('uthsc_wpcas_context'));
			
			// For production use set the CA certificate that is the issuer of the cert
			// on the CAS server and uncomment the line below
			//phpCAS::setCasServerCACert(get_option('uthsc_wpcas_cert_path'));

			// For quick testing you can disable SSL validation of the CAS server.
			// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
			// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
			// TODO PTR
			phpCAS::setNoCasServerValidation();

			// Handle SAML logout requests that emanate from the CAS host exclusively.
			// Failure to restrict SAML logout requests to authorized hosts could
			// allow denial of service attacks where at the least the server is
			// tied up parsing bogus XML messages.
			phpCAS::handleLogoutRequests(true, array('cas-real-1.example.com', 'cas-real-2.example.com'));

			// Uncomment to enable debugging		
			phpCAS::setDebug( dirname( __FILE__ ) . "/cas.log" );

		}

		public function authenticate($login_url) {
			error_log("authenticate... ", 0);
			if ( phpCAS::isAuthenticated() ) {

				$cas_user = phpCAS::getUser();
				$cas_attributes = phpCAS::getAttributes();

				// error_log("user: " . $cas_user, 0);
				// error_log("cas_attributes: " . implode ( "# " , $cas_attributes ), 0);

				//This is based on the CAS reponse; it may be different for your configuration.
				//To test, you can use var_dump($cas_attributes)
				$userdata = array (



					// TODO PTR make all attibutes optional: last_name, first_name, user_email, user_nicename !!!



				'user_login'		=>	$cas_user,
				'last_name'			=>	$cas_attributes[get_option('uthsc_wpcas_last_name')],
				'first_name'		=>	is_array( $cas_attributes[get_option('uthsc_wpcas_first_name')] ) ? $cas_attributes[get_option('uthsc_wpcas_first_name')]['1'] : $cas_attributes[get_option('uthsc_wpcas_first_name')],
				'user_email'		=>	$cas_attributes[get_option('uthsc_wpcas_user_email')],
			    'user_nicename'		=>	(get_option('uthsc_wpcas_nickname')) ? 
			            (isset($cas_attributes[get_option('uthsc_wpcas_nickname')]) ? $cas_attributes[get_option('uthsc_wpcas_nickname')] : 
			            	"") : ""
				);

				//If the user hasn't logged in to Wordpress before, create an account with the Attributes returned by cas
				if ( !get_user_by( 'login', $cas_user ) ) {
					$user_pass = wp_generate_password( 12, false );
					$userdata['user_pass'] = $user_pass;
					$userdata = $this->prepareNicename($userdata);
					wp_insert_user( $userdata );
				} else if(get_option('uthsc_wpcas_update_acct') == 'on'){
					$account = get_user_by( 'login', $cas_user );
					$userdata['ID'] = $account->get('ID');
					if ( $this->hasChangedAttributes($userdata, $account) ){
						error_log("Rewriting wordpress profile. " . $cas_user, 0);
						$userdata = $this->prepareNicename($userdata);
						wp_update_user( $userdata );
					}
				}
				
				return get_user_by( 'login', $cas_user); // was: $cas_attributes['uid'] );

			} else {
				//trim reauth from service url to prevent users from being redirected back to WP login screen after logging in.
	 			if ( preg_match("/&reauth=1/", $login_url) ) {
				  $login_url = rtrim($login_url,'&reauth=1');
				}
				
				//return the login url
				return 'https://'. get_option('uthsc_wpcas_host') . get_option('uthsc_wpcas_context') . '/login?service='. $login_url;
				
			}

		}

		/* set's the "user_nicename" attribute. */
		function prepareNicename($userdata){
			if(! $userdata['user_nicename']){
				$userdata['user_nicename'] = $userdata['first_name']. " ". $userdata['last_name'];
			}
			$userdata['nickname'] = $userdata['user_nicename'];
			error_log("Setting nickname: <" . $userdata['user_nicename'].">", 0);
			return $userdata;
		}

		/* Checks whether one or more userattributes differ from the WP profile data. except 'user_nicename'*/
		 function hasChangedAttributes($userdata, $account) {
			foreach ($userdata as $key => $value){
				if($value !== $account->get($key) && $key!=="user_nicename"){
					error_log("Attribute changed: " . $key. ": ".$value."!=".$account->get($key)  , 0);
					return true;
				}
			}
			return false;
		}

		public static function readCasUID() {
			$cas_user = phpCAS::getUser();
			$cas_attributes = phpCAS::getAttributes();
			$uid_option = get_option('uthsc_wpcas_uid');

			error_log("$cas_user: " . $cas_user , 0);
			error_log("$uid_option: " . $uid_option , 0);
			$result = ($uid_option === "cas_user") ? $cas_user : $cas_attributes[$uid_option];
			if( ! $result ){
				echo 'Did not find cas_attribute ['. $uid_option. ']. Check WPCAS configuration "UID"';
			} 
			return $result;
		}

		//Custom logout function to bypass WordPress default logout and provide a custom redirect target (needs work).
		public function logout(){

			wp_set_current_user(0);
			wp_clear_auth_cookie();

			if ($_GET['action']=='logout') {
				if (isset($_GET['redirect_to']) && $_GET['redirect_to']) {
					phpCAS::logoutWithRedirectService($_GET['redirect_to']);
				} else {
					phpCAS::logoutWithRedirectService(site_url());
				}
			}
			exit();
		}

		//Disables display of password fields in the user profile page.
		//We don't use WP authentication, so we don't need to worry about any WP-specific passwords.
		public function show_password_fields( $show_password_fields ) {
			return false;
		}

		//Utility function to disable WP behaviors.
		public function disable_function() {
			die('Disabled');
		}
	}
	 
}
// register WPCAS widget
require_once('wpsso-widget.php');
add_action('widgets_init', create_function('', 'return register_widget("wp_sso_widget");'));

register_activation_hook( __FILE__, array( 'UTHSCWPCAS', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'UTHSCWPCAS', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'UTHSCWPCAS', 'uninstall' ) );

//Load plugin if not 'doNativeLogin'
if( strpos($_SERVER["REQUEST_URI"], "?doNativeLogin=true") == false ){		
	$uthscwpcas = new UTHSCWPCAS();
}