=== Grab Latest Track From SoundCloud ===
Contributors: dmiyares
Tags: widget, podcast, soundcloud, rss
Requires at least: 5.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget to pull & display latest track from your SoundCloud RSS Feed.  Requires a SoundCloud RSS feed.

== Description ==
Adds a widget that pulls a SoundCloud RSS feed and generates all the needed code to display the latest track where ever you place the widget.  The plugin will setup a CRON job to check once an hour for the newest track.  Users are also able to manually execute an RSS pull and not wait an hour.  

Why did we create this plugin?  We needed a way to remove the weekly manual updating of podcasts.  With more than a dozen site each with their own weekly podcast updating the widget often got forgotten leaving promo for outdated ie: stale podcasts.  This plugin solves that problem.  Editors now simply have to recode, edit, & publish on SoundCloud.


== Installation ==
1. Install `Grab Latest Track From SoundCloud` either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin.
1. Under the available widgets you will now see `Newest SoundCloud Track\'


== Frequently Asked Questions ==
**Q: I just added my feed by it\'s showing up as an error on the front end**
A: On the widgets dialog box click the `Click here to import newest Podcasts NOW` link.  This will force WordPress to go and gather the newest items. 

**Q: Does this plugin work with providers other than SoundCloud**
A: No.  This plugin was specifically made to pull RSS from SoundCloud

**Q: Where can I find my SoundCloud RSS feed\'s URL?** 
A: Login to your SoundCloud account and go under settings -> Content.  The RSS feed will be displayed here.

**Q: The Date / Title are pushed up right against the DIV and look terrible! How can I change this?
A: You can tweek the way the Date & the Title show up using the WordPress Customizing -> Additional CSS.  
	 There are two classes for these.  So adding something like the snip below will push everything over to the right
	 
	 		.SoundCloudPubDate{padding:10px}
			.SoundCloudTitle{padding:10px}


== Screenshots ==
1. the main dialogue box showing customizable fields
2. Example of how widget looks on front end in right rail

== Changelog ==
= 1.0.0 =
Release Date: June 23rd, 2021

Initial release of plugin

== Upgrade Notice ==
Nothing