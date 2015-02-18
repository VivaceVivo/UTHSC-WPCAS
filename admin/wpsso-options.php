<?php

//Stores settings groups, options and defaults for the plugin
class WPSSO_Options {

	function wpsso_settings() {

		$settings = array(
				'wpsso-configuration' 	=> array (
					'wpsso_host'		=> 'localhost',
					'wpsso_user_email'	=> '',
					'wpsso_uid'			=> 'cas_user', // returns the $cas_user or the $cas_attributes[get_option('wpsso_uid')]
					'wpsso_first_name'	=> 'firstname',
					'wpsso_last_name'	=> 'lastname',
					'wpsso_nickname'	=> 'nickname',
					'wpsso_nickname_realname' => 'off',
					'wpsso_context'		=> '/cas',
					'wpsso_cert_path'	=> str_replace( 'admin/','',plugin_dir_path( __FILE__) )  . 'caskey/cacerts_auth.pem',
					'wpsso_port'		=> '443',
				),
				'wpsso-plugin-options'	=> array (
					'wpsso_update_acct'	=> 'off',
					// 'wpsso_lockdown'	=> 'off',
					// 'wpsso_restrict_new_users'	=> 'off',
					'wpsso_native_login_url_pattern' => 'wordpress/wp-admin',
					'wpsso_profil_page' => ''
				)
			);

		return $settings;

	}

}