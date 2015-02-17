<?php

function uthsc_wpcas_test() {

	// Force CAS authentication on any page that includes this file
	//phpCAS::forceAuthentication();
	
	// Some small code triggered by the logout button
	if (isset($_REQUEST['logout'])) {
	  phpCAS::logoutWithRedirectService('http://news.uthsc.edu');
	}

	?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2>UTHSC WP CAS Test</h2>

		<p>If Customer 360 - WPCAS and your CAS server are configured properly, this page should display the attributes returned by CAS.</p>

		<h3>Session Info</h3>
		<p>Some session info. Because, why not?</p>
		<ul>
			<li>Script Name : <strong><?php print basename($_SERVER['SCRIPT_NAME']); ?></strong></li>
			<li>Session Name: <strong> <?php print session_name(); ?></strong></li>
			<li>Session ID: <strong> <?php print session_id(); ?></strong></li>
		</ul>

		<h3>User Attributes</h3>
		<p>These are the attributes returned by CAS and they may vary depending on what your CAS server is configured to return.<br />
		If you want to use these attributes for new WordPress user accounts (firstname, lastname, email), 
		the index names here should match the ones on the <a href="?page=uthsc-wpcas-preferences">CAS Settings page</a>.<br />
		If your cas server is not configured to return the attributes, they will be blank and will need to be entered manually.
		These attributes are not required for new user accounts to be created; all that is needed is the UID returned by cas. 
		</p>

		<ul>

			<?php
				
			$error = '
			<p style="color:orange;"><strong> phpCAS::isAuthenticated() returned false. 
			Try logging in with a SSO-Account (not native login).  
			</strong></p>';

			if (phpCAS::isAuthenticated()) {

				foreach (phpCAS::getAttributes() as $key => $value) {
					if (is_array($value)) {
						
						echo '<li>', $key, ':<ol>';				
						foreach ($value as $item) {
							echo '<li><strong>', $item, '</strong></li>';
						}
						echo '</ol></li>';

					} else {
				
						echo '<li>', $key, ': <strong>', $value, '</strong></li>' . PHP_EOL;

					}
				}

			} else {

				echo $error;

			}

			?>

		</ul>

		<h3>WP User Account</h3>
		<p>These are the attributes of the WordPress user that corresponds with the UID returned by CAS.</p>
		<ul>

			<?php

			if (phpCAS::isAuthenticated()) {

				$cas_attributes = phpCAS::getAttributes();
				
				$wp_attributes = get_user_by( 'login', UTHSCWPCAS::readCasUID());
				
				$wp_display_attributes = array('ID', 'user_nicename', 'user_login', 'user_email', 'user_registered', 'user_url', 'display_name', 'user_status', 'user_activation_key');

				foreach ($wp_display_attributes as $attribute) {
					echo '<li>' . $attribute . ': <strong>' . $wp_attributes->data->$attribute . '</strong></li>';
				}

			}	else {

				echo $error;

			}
			
			?>

		</ul>

		<h3>WP User Meta Data</h3>
		<p>These are some meta attributes of the WordPress user that corresponds with the UID returned by CAS.</p>
		<ul>

			<?php

			if (phpCAS::isAuthenticated()) {
				$wp_meta_attrs = array('nickname', 'first_name', 'last_name', 'description');
				foreach ($wp_meta_attrs as $meta){
					echo '<li>'. $meta. ': <strong>'. get_user_meta($wp_attributes->ID, $meta, true). '</strong></li>';
				}
			}	else {

				echo $error;

			}
			
			?>
	</div>

<?php
} //close admin options page
