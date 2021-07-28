<?php
/**
 * Plugin Name: Grab Latest Track From SoundCloud
 * Plugin URI: 	https://marketing.wtwhmedia.com/plugins/
 * Description: Adds a widget that pulls a SoundCloud RSS feed and generates all the needed code to display the latest track where ever you place the widget.  The plugin will setup a CRON job to check once an hour for the newest track.
 * Version:     1.0.0
 * Author:      WTWH Media LLC - B. David Miyares
 * Author URI:  https://marketing.wtwhmedia.com/contact-us/
 * License: GNU GPLv2
 * GitHub : https://github.com/dmiyares/SoundCloud-Latest-Track-Grabber
 *
 */

// create the Widget
class wtwh_soundcloud extends WP_Widget {

 
	protected $defaults;
 
	function __construct() {

		// widget defaults
		$this->defaults = array(
						'title'          => 'Featured Programming',
            'more_url'       => '',
		);

		// Widget Slug
		$widget_slug = 'wtwh_soundcloud_widget';

		// widget basics
		$widget_ops = array(
			'classname'   => $widget_slug,
			'description' => ''
		);

		// widget controls
		$control_ops = array(
			'id_base' => $widget_slug,
			'width'   => '300',
		);

		// load widget
		parent::__construct( $widget_slug, 'Newest SoundCloud Track', $widget_ops, $control_ops );

	}

	// stuff for front pages
	
	function widget( $args, $instance ) {

		 
		extract( $args );

		// Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$widgetID=$args['widget_id'];
 
 
 		$widget_number=$res = preg_replace("/[^0-9]/", "", $widgetID );
		$sound_cloud_track_id =get_option('wtwh_soundcloud_trackID-'.$widget_number);
		$sound_cloud_pub_date =get_option('wtwh_soundcloud_pubDateID-'.$widget_number);
		$sound_cloud_title   =get_option('wtwh_soundcloud_title-'.$widget_number);
		
	 
		
		
		
		echo $before_widget;

			// Title
			if ( !empty( $instance['title'] ) ) {
               
				echo $before_title . apply_filters( 'widget_title', esc_attr($instance['title']) ) .  $after_title;
			}

             
                echo '<div class="wtwh-soundcloud-player-wrapper"><div class="wtwh-soundcloud-player">';
          
                
               
				  echo '<div>';
				 
				 
				echo '<iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.esc_attr($sound_cloud_track_id).'&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>';

		if($instance['show_date']=="Y"){
			echo '<div class="sound_cloud_pub_date">'.esc_attr($sound_cloud_pub_date).'</div>';}
		if($instance['show_title']=="Y"){
			echo '<div class="sound_cloud_title">'.esc_attr($sound_cloud_title).'</div>';}
			
			echo '</div>';
    			 
    		 	if(!empty( $instance['more_url'] )){ echo ' <a href="' . esc_url( $instance['more_url'] ) . '" class="sound_cloud_title sound_cloud_pub_date" target="_blank">See More ></a>';};
    			  
    			   
                echo '</div></div>';
             

		echo $after_widget;
	}

	 
	function update( $new_instance, $old_instance ) {
	
	// save the details of the news item in the rss feed to the Options table.
	
		$new_instance['title']      = strip_tags( $new_instance['title'] );
		$new_instance['rssfeed']    = esc_url( $new_instance['rssfeed'] );
		$new_instance['more_url']   = esc_url( $new_instance['more_url'] );
		$new_instance['show_date']  =  $new_instance['show_date'];
		$new_instance['show_title'] =  $new_instance['show_title'];
		
 
		return $new_instance;
		
		
	}

	 
	function form( $instance ) {

	
		$instance = wp_parse_args( (array) $instance, $this->defaults );
  
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>">Title:</label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		<p>
			
			<label for="<?php echo esc_url($this->get_field_id( 'rssfeed' )); ?>">RSS Feed URL:</label>
			
			<input type="text" id="<?php echo esc_url($this->get_field_id( 'rssfeed' )); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rssfeed' )); ?>" value="<?php echo esc_url( $instance['rssfeed'] ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_url($this->get_field_id( 'more_url' )); ?>">More URL:</label>
			<input type="text" id="<?php echo esc_url($this->get_field_id( 'more_url' )); ?>" name="<?php echo esc_url($this->get_field_name( 'more_url' )); ?>" value="<?php echo esc_url( $instance['more_url'] ); ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'show_title' )); ?>">Show Title:</label>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id( 'show_title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_title' )); ?>" value="Y" 
			<?php if( $instance['show_title']==="Y"){echo ' checked ';}?>/>
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>">Show Date:</label>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_date' )); ?>" value="Y" 
			<?php if( $instance['show_date']==="Y"){echo ' checked ';}?>/>
		</p>
		

		<?php
					// Since we have a CRON job running we need to allow users to pull the feed ASAP after creation otherwise there will be a SoundCloud error box in the widget area.
					
			if( $instance['rssfeed']>''){
				$SNonce=wp_nonce_url('?wtwhSoundcloudPullNow=PullNow','dosomething','_syndicate');
			?>
	      <p><?php _e( '<a href="'.$SNonce.'">Click here to import newest Podcasts NOW</a>'); ?></p>
  		<?php 
			
			}

	}
}


// functions

function wtwh_soundcloud_activate_plugin(){
	if( version_compare(get_bloginfo('version'),'5.7.2', '<'))	{
			wp_die('This plugin will not work on this version of WordPress. You need 5.7.2 or better');
			}
		global $wpdb;
		wp_schedule_event(time(),'hourly', 'wtwh_soundcloud_cron_job_setup_to_pull_rss_feed'); // set cron job to pull new items from Sound Cloud once every hour.  
		
		}
		
		
function wtwh_soundcloud_deactivate_plugin(){
				wp_clear_scheduled_hook('wtwh_soundcloud_cron_job_setup_to_pull_rss_feed');
				
				// not deleting anything... don't want to be the bad guy
			}

// Pullin RSS feed and get first item from each.



function wtwh_soundcloud_pull_rss_feed($WidgetID, $FeedURL){
	
	$rss_items = null;
	$rss = fetch_feed($FeedURL);   
 
 		if (!is_wp_error( $rss ) ) :
		    $maxitems = $rss->get_item_quantity(1);
		    $rss_items = $rss->get_items(0, $maxitems);
		endif;
		
		 if($rss_items) {	
	   foreach ( $rss_items as $item ){ 
	 
  // here's pretty much everything we can pull from soundcould RSS feed 
  #  echo esc_url( $item->get_permalink() );               print("\n");
  #  echo 'Posted '.$item->get_date('j F Y | g:i a');      print("\n");
  #  echo esc_html( $item->get_title() );                  print("\n");
  #  echo $item->get_base();                               print("\n");
  #  echo $rss->get_title();                               print("\n");
  #  echo $item->get_description();                        print("\n");
    
    
    preg_match('/tag\:soundcloud\,2010\:tracks\/(.*)/', $item->get_id(), $matches);
    
    $SoundcloudID="wtwh_soundcloud_trackID-".$WidgetID;
    $sound_cloud_pub_date="wtwh_soundcloud_pubDateID-".$WidgetID;
    $sound_cloud_title="wtwh_soundcloud_title-".$WidgetID;
    
    update_option( $SoundcloudID,$matches[1], true );
    update_option( $sound_cloud_pub_date,$item->get_date('F j, Y'), true );
    update_option( $sound_cloud_title,esc_html( $item->get_title() ), true );
    
   
   
 }
}    	 
} 


function wtwh_soundcloud_pull_feeds_from_options(){	
				$Feeds=get_option('widget_wtwh_soundcloud_widget');
				 
					foreach($Feeds as $WidgetID => $feed_url){
 
 						$FeedURL=wtwh_soundcloud_pull_rss_feed($WidgetID, $feed_url['rssfeed']);
				 }
		}
 
 
function wtwh_soundcloud_initial_pull(){
	
	$wtwhSoundcloudPullNow =    (isset($_GET['wtwhSoundcloudPullNow'])     ? $_GET['wtwhSoundcloudPullNow']  : null);
	if(is_admin()){

	if($wtwhSoundcloudPullNow=="PullNow"){
 			wtwh_soundcloud_pull_feeds_from_options();
		}
	}
}


function wtwh_soundcloud ()
{
    return register_widget('wtwh_soundcloud');
}



 

 // Hooks
 
		add_action ('widgets_init', 'wtwh_soundcloud');
	 	add_action('admin_notices', 'wtwh_soundcloud_initial_pull' );
		register_activation_hook(__FILE__, 'wtwh_soundcloud_activate_plugin');
		register_deactivation_hook(__FILE__, 'wtwh_soundcloud_deactivate_plugin');
		add_action('wtwh_soundcloud_cron_job_setup_to_pull_rss_feed', 'wtwh_soundcloud_pull_feeds_from_options');
		
 