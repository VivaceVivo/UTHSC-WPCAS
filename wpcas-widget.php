<?php

class wp_sso_widget extends WP_Widget {
	 
    // constructor
    function wp_sso_widget() {
        parent::WP_Widget(false, $name = __('SSO Widget', 'wp_widget_plugin') );
    }
 
    // widget form creation
    function form($instance) {
     
	    // Check values
	    if( $instance) {
	         $title = esc_attr($instance['title']);
	    } else {
		     $title = '';	   
		}
		?>
		 
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		?></textarea>
	    </p>	

	    <?php	
	}
 
    // update widget
	function update($new_instance, $old_instance) {
	      $instance = $old_instance;
	      // Fields
	      $instance['title'] = strip_tags($new_instance['title']);
	     return $instance;
	}
 
   // display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	  
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text wp_widget_plugin_box">';
	 
	   // Check if title is set
	   if ( $title ) {
	      echo $before_title . $title . $after_title;
	   }
	 ?>
	   <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <?php if ( is_user_logged_in() && get_option('uthsc_wpcas_profil_page')) {  ?>
            	<li><a href="<?php echo get_option('uthsc_wpcas_profil_page'); ?>?service=<?php echo home_url() ?>">Profil</a></li>
            <?php } ?>
        </ul>
	   </div>
	   <?php
	   echo $after_widget;
	}

}