<?php

function wpsso_about() {

	?>

	<div class="wrap">
		<h2>Customer 360 - WP-SSO</h2>
		<h3>This plugin enables WP login with the Customer 360 CAS Server. It is based on the UTHSC WPCAS-Plugin.</h3>
		
		<p>The plugin was extended to support also the older CAS-protocol and adds some features:</p>
		<ol>
			<li>configurable mapping of the CAS-UID</li>
			<li>update of the wordpress profile on each login</li>
			<li>Hybrid login (wordpress native login for selected path)</li>
			<li>WP-CAS widget with login/logout, profile and registration link</li>
		</ol>
		<p>This plugin was developed for the CGI "Customer 360" project to enable CAS-login for any wordpress site.</p>
	
		<p>Although this plugin was developed with the "Customer 360" CAS server in mind, it should be able to utilize 
		   any CAS compatible authentification server.
		</p>
	
		<p>If you need more info, have ideas or would like to contribute check out the <a href="https://github.com/">TODO: Customer 360 - WP-SSO Repo</a> on GitHub</p>
	
	</div>

	<?php

} //close admin options page
