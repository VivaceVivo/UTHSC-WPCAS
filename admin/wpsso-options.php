<?php

//Stores settings groups, options and defaults for the plugin
class WPSSO_Options {

	function wpsso_settings() {

		$settings = array(
				'uthsc-wpcas-configuration' 	=> array (
					'uthsc_wpcas_host'			=> 'localhost',
					'uthsc_wpcas_user_email'	=> '',
					'uthsc_wpcas_uid'			=> 'cas_user', // returns the $cas_user or the $cas_attributes[get_option('uthsc_wpcas_uid')]
					'uthsc_wpcas_first_name'	=> 'firstname',
					'uthsc_wpcas_last_name'		=> 'lastname',
					'uthsc_wpcas_nickname'		=> 'nickname',
					'uthsc_wpcas_context'		=> '/cas',
					'uthsc_wpcas_cert_path'		=> str_replace( 'admin/','',plugin_dir_path( __FILE__) )  . 'caskey/cacerts_auth.pem',
					'uthsc_wpcas_port'			=> '443',
				),
				'uthsc-wpcas-plugin-options'	=> array (
					'uthsc_wpcas_update_acct'	=> 'off',
					'uthsc_wpcas_lockdown'		=> 'off',
					'uthsc_wpcas_restrict_new_users'	=> 'off',
					'uthsc_wpcas_native_login_url_pattern' => 'wordpress/wp-admin',
					'uthsc_wpcas_profil_page' => ''
				)
			);

		return $settings;

	}

}