<?php

function uthsc_wpcas_about() {

	?>

	<div class="wrap">
		<h2>Customer 360 - WPCAS</h2>
		<h3>This plugin enables WP login with the Customer 360 CAS Server. It is based on the UTHSC WPCAS-Plugin.</h3>
		
		<p>The plugin was extended to support also the older CAS-protocol and adds some features:</p>
		<ol>
			<li>configurable mapping of the CAS-UID</li>
			<li>update of the wordpress profile on each login</li>
			<li>Hybrid login (wordpress native login for selected path)</li>
			<li>WP-CAS widget with login/logout, profile and registration link</li>
		</ol>
		<p>This plugin was developed for the University of Tennessee Health Science Center to integrate CAS with UTHSC WordPress sites.</p>
	
		<p>After looking around for existing plugins we realized that there were very few CAS plugins for WordPress and most of them were not being actively maintained.<br />
		IU-WPCAS, developed by David Poindexter at Indiana University, was the closest thing we found to a working option but since it was built for IU we had a tough time getting it to work for us.<br />
		UTHSC-WPCAS uses the phpCAS library and most of the configuration settings can be modified in the WordPress admin section.<br />
		We hope that this will serve as an ongoing project for the CAS/WordPress community that will work for anyone who needs it.
		</p>
	
		<p>If you need more info, have ideas or would like to contribute check out the <a href="https://github.com/uthsc/uthsc-wpcas">UTHSC-WPCAS Repo</a> on GitHub</p>
	
	</div>

	<?php

} //close admin options page
