<?php

function wpsso_preferences() {
	
?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>

		<h2>Customer 360 - WPCAS Preferences</h2>

		<?php 
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'cas-configuration';  
		?>

		<h2 class="nav-tab-wrapper">  
		  <a href="?page=wpsso-settings&tab=cas-configuration" class="nav-tab <?php echo $active_tab == 'cas-configuration' ? 'nav-tab-active' : ''; ?> ">CAS Configuration</a>  
		  <a href="?page=wpsso-settings&tab=wpsso-options" class="nav-tab <?php echo $active_tab == 'wpsso-options' ? 'nav-tab-active' : ''; ?> ">WPSSO Options</a>  
		</h2> 

		<form method="post" action="options.php">
			<?php
				
			if( $active_tab == 'cas-configuration' ) {
				
				settings_fields( 'wpsso-configuration' );
				
				?>

				<fieldset>
					<h3>CAS Host</h3>
					<p>Defaults to "auth.uthsc.edu"</p>
					<ul>
						<li>
							<label for="wpsso_host">CAS Host</label>
							<input 
								type="text" 
								name="wpsso_host" 
								value="<?php echo get_option('wpsso_host')?>"
								id="wpsso_host" 
								/>
							
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>CAS Context</h3>
					<p>Defaults to "/cas"</p>
					<ul>
						<li>
							<label for="wpsso_context">CAS Context</label>
							<input 
								type="text" 
								name="wpsso_context" 
								value="<?php echo get_option('wpsso_context')?>"
								id="wpsso_context" 
								/>
							
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>CAS Port</h3>
					<p>Defaults to 443</p>
					<ul>
						<li>
							<label for="wpsso_port">CAS Port</label>
							<input
								type="text" 
								name="wpsso_port" 
								value="<?php echo get_option('wpsso_port')?>"
								id="wpsso_port" 
							/>
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>CAS Certificate Path</h3>
					<p>Path to CAS Cert</p>
					<ul>
						<li>
							<label for="wpsso_cert_path">CAS Cert Path</label>
							<input
								size="70"
								type="text" 
								name="wpsso_cert_path" 
								value="<?php echo get_option('wpsso_cert_path')?>"
								id="wpsso_cert_path" 
								/>
							
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>CAS Attributes</h3>
					<p>Array indexes returned by cas that will be used as args for wp_insert() when new users are created. WordPress username will use uid returned by CAS.</p>
					<ul>
						<li>
							<label for="wpsso_uid">UID</label>
							<input
								type="text"
								name="wpsso_uid"
								value="<?php echo get_option('wpsso_uid')?>"
								id="wpsso_uid"
							/>
						</li>
						<li>
							<label for="wpsso_first_name">First Name</label>
							<input
								type="text"
								name="wpsso_first_name"
								value="<?php echo get_option('wpsso_first_name')?>"
								id="wpsso_first_name"
							/>
						</li>

						<li>
							<label for="wpsso_last_name">Last Name</label>
							<input
								type="text"
								name="wpsso_last_name"
								value="<?php echo get_option('wpsso_last_name')?>"
								id="wpsso_last_name"
							/>
						</li>

						<li>
							<label for="wpsso_user_email">Email</label>
							<input
								type="text"
								name="wpsso_user_email"
								value="<?php echo get_option('wpsso_user_email')?>"
								id="wpsso_user_email"
							/>
						</li>

						<li>
							<label for="wpsso_nickname">Nickname</label>
							<input
								type="text"
								name="wpsso_nickname"
								value="<?php echo get_option('wpsso_nickname')?>"
								id="wpsso_nickname"
							/>
							<label for="wpsso_nickname_realname_off">WP default</label>
								<input type="radio" name="wpsso_nickname_realname" id="wpsso_nickname_realname_off" value="off" <?php  echo get_option('wpsso_nickname_realname') == 'off' ? 'checked="checked"' : '' ?> />
								<label for="wpsso_update_acct_on">use real name</label>
							<input type="radio" name="wpsso_nickname_realname" id="wpsso_nickname_realname_on" value="on"  <?php  echo get_option('wpsso_nickname_realname') == 'on' ? 'checked="checked"' : '' ?> />
						</li>
					</ul>
				</fieldset>

				<?php

			} else {

				settings_fields( 'wpsso-plugin-options' );

			
				/*
				<fieldset>
					<h3>To Do: CAS Lockdown</h3>
					<p>If this is turned on, users will be forced to log in to see the site</p>
					<ul>
						<li>
								<label for="wpsso_lockdown_off">Off</label>
								<input type="radio" name="wpsso_lockdown" id="wpsso_lockdown_off" value="off" <?php  echo get_option('wpsso_lockdown') == 'off' ? 'checked="checked"' : '' ?> />
								<label for="wpsso_lockdown_on">On</label>
								<input type="radio" name="wpsso_lockdown" id="wpsso_lockdown_on" value="on"  <?php  echo get_option('wpsso_lockdown') == 'on' ? 'checked="checked"' : '' ?> />
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>To Do: Restrict New Users</h3>
					<p>Users must already have a WordPress account on the site to login<br /></p>
					<ul>
						<li>
								<label for="wpcas_restrict_new_users_off">Off</label>
								<input type="radio" name="wpsso_restrict_new_users" id="wpsso_restrict_new_users_off" value="off" <?php  echo get_option('wpsso_restrict_new_users') == 'off' ? 'checked="checked"' : '' ?> />
								<label for="wpcas_restrict_new_users_on">On</label>
								<input type="radio" name="wpsso_restrict_new_users" id="wpsso_restrict_new_users_on" value="on"  <?php  echo get_option('wpsso_restrict_new_users') == 'on' ? 'checked="checked"' : '' ?> />
						</li>
					</ul>
				</fieldset>
				*/
				?>
				<fieldset>
					<h3>Update WordPress Account on Login</h3>
					<p>Updates WordPress profile on every login.</p>
					<ul>
						<li>
								<label for="wpsso_update_acct_off">Off</label>
								<input type="radio" name="wpsso_update_acct" id="wpsso_update_acct_off" value="off" <?php  echo get_option('wpsso_update_acct') == 'off' ? 'checked="checked"' : '' ?> />
								<label for="wpsso_update_acct_on">On</label>
								<input type="radio" name="wpsso_update_acct" id="wpsso_update_acct_on" value="on"  <?php  echo get_option('wpsso_update_acct') == 'on' ? 'checked="checked"' : '' ?> />
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>Customer 360 - Profile page</h3>
					<p>Url of the (optional) CAS-Profile page. </p>
					<ul>
						<li>
								<label for="wpsso_profil_page">URL</label>
								<input
								type="text"
								name="wpsso_profil_page"
								value="<?php echo get_option('wpsso_profil_page')?>"
								id="wpsso_profil_page"
							/>
						</li>
					</ul>
				</fieldset>

				<fieldset>
					<h3>Native Login</h3>
					<p>Pages matching the given pattern use the standard WordPress login. (leave empty to turn off native login)</p>
					<ul>
						<li>
								<label for="wpsso_native_login_url_pattern">substring</label>
								<input
								type="text"
								name="wpsso_native_login_url_pattern"
								value="<?php echo get_option('wpsso_native_login_url_pattern')?>"
								id="wpsso_native_login_url_pattern"
							/>
						</li>
					</ul>
				</fieldset>

				<?php	

			} // end if/else

			submit_button();

			?>

		</form>

	</div>

	<?php

} //close admin options page