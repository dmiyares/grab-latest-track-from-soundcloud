<?php
/**
 * Plugin Name: Grab Latest Track From SoundCloud
 * Plugin URI: 	https://www.wtwhmedia.com/plugins
 * Description: Adds a widget that pulls a SoundCloud RSS feed and generates all the needed code to display the latest track where ever you place the widget.  The plugin will setup a CRON job to check once an hour for the newest track.
 * Version:     1.0.0
 * Author:      WTWH Media LLC - B. David Miyares
 * Author URI:  https://www.wtwhmedia.com 
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
 
 
 		$WidgetNumber=$res = preg_replace("/[^0-9]/", "", $widgetID );
		$SoundCloudTrackID =get_option('wtwh_soundcloud_trackID-'.$WidgetNumber);
		$SoundCloudPubDate =get_option('wtwh_soundcloud_pubDateID-'.$WidgetNumber);
		$SoundCloudTitle   =get_option('wtwh_soundcloud_title-'.$WidgetNumber);
		
	 
		
		
		
		echo $before_widget;

			// Title
			if ( !empty( $instance['title'] ) ) {
               
				echo $before_title . apply_filters( 'widget_title', $instance['title'] ) .  $after_title;
			}

             
                echo '<div class="article-listing"><div class="row">';
          
                
               
				  echo '<div >';
				 
				 
				echo '<iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$SoundCloudTrackID.'&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>';

		if($instance['show_date']=="Y"){
			echo '<div class="SoundCloudPubDate">'.$SoundCloudPubDate.'</div>';}
		if($instance['show_title']=="Y"){
			echo '<div class="SoundCloudTitle">'.$SoundCloudTitle.'</div>';}
				 
			 
					echo '</div>';
    			  $more = !empty( $instance['more_url'] ) ? ' <a href="' . esc_url( $instance['more_url'] ) . '">See More ></a>' : '';
    			  
    			  echo $more;
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
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'rssfeed' ); ?>">RSS Feed URL:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'rssfeed' ); ?>" name="<?php echo $this->get_field_name( 'rssfeed' ); ?>" value="<?php echo esc_attr( $instance['rssfeed'] ); ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'more_url' ); ?>">More URL:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'more_url' ); ?>" name="<?php echo $this->get_field_name( 'more_url' ); ?>" value="<?php echo esc_attr( $instance['more_url'] ); ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'show_title' ); ?>">Show Title:</label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="Y" 
			<?php if( $instance['show_title']==="Y"){print(' checked ');}?>/>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">Show Date:</label>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" value="Y" 
			<?php if( $instance['show_date']==="Y"){print(' checked ');}?>/>
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

	$rss = fetch_feed($FeedURL);   
	
	
		if (!is_wp_error( $rss ) ) :
		    $maxitems = $rss->get_item_quantity(1);
		    $rss_items = $rss->get_items(0, $maxitems);
		endif;
	   foreach ( $rss_items as $item ){ 
	  	
  // here's pretty much everything i can pull from soundcould RSS feed 
  # 	echo esc_url( $item->get_permalink() );               print("\n");
  # 	echo 'Posted '.$item->get_date('j F Y | g:i a');      print("\n");
  # 	echo esc_html( $item->get_title() );                  print("\n");
  # 	echo $item->get_base();                               print("\n");
  #  echo $rss->get_title();                               print("\n");
  #  echo $item->get_description();                        print("\n");
    
    
    preg_match('/tag\:soundcloud\,2010\:tracks\/(.*)/', $item->get_id(), $matches);
    
    $SoundcloudID="wtwh_soundcloud_trackID-".$WidgetID;
    $SoundcloudPubDate="wtwh_soundcloud_pubDateID-".$WidgetID;
    $SoundcloudTitle="wtwh_soundcloud_title-".$WidgetID;
    
    update_option( $SoundcloudID,$matches[1], true );
    update_option( $SoundcloudPubDate,$item->get_date('g:i a - F j Y'), true );
    update_option( $SoundcloudTitle,esc_html( $item->get_title() ), true );
    
    
    
   
   
 }
      	 
} 


function wtwh_soundcloud_pull_feeds_from_options(){
	
	
				$Feeds=get_option('widget_wtwh_soundcloud_widget');
					foreach($Feeds as $WidgetID => $Feed){
						 	$FeedURL=wtwh_soundcloud_pull_rss_feed($WidgetID, $Feed['rssfeed']);
					}
		}
 
 
function wtwh_soundcloud_initial_pull(){
	if(is_admin()){
	if($_GET['wtwhSoundcloudPullNow']=="PullNow"){
 			wtwh_soundcloud_pull_feeds_from_options();
		}
	}
}
 // Hooks

		add_action( 'widgets_init', create_function( '', "register_widget('wtwh_soundcloud');" ) );
	 	add_action('admin_notices', 'wtwh_soundcloud_initial_pull' );
		register_activation_hook(__FILE__, 'wtwh_soundcloud_activate_plugin');
		register_deactivation_hook(__FILE__, 'wtwh_soundcloud_deactivate_plugin');
		add_action('wtwh_soundcloud_cron_job_setup_to_pull_rss_feed', 'wtwh_soundcloud_pull_feeds_from_options');
		
 