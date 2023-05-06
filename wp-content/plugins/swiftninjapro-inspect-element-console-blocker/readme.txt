=== Developer Tools Blocker ===
Contributors: SwiftNinjaPro
Tags: console, block, inspect, element, browser, stop, hide, devtools, dev, developer, fast, accurate
Requires at least: 3.0.1
Tested up to: 5.8.2
Stable tag: 3.2.1
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://buymeacoffee.swiftninjapro.com

This plugin blocks non-admin users from using inspect element, while still allowing access those with manage_options permission.
The plugin also blocks some of the common keys(f12, ect.) and optionally can block right-clicks for non-admin users. 
Admins are unaffected by the plugin entirely. This plugin is also able to block users who open developer tools on another site, before visiting your site.

Version 3.0 update

This plugin is now fast and accurate, and should work on most browsers.
This plugin can detect the browser console based on lag increased by opening the console. This detection is tough for a browser to prevent or get around.
The detection runs fast, and has some resistance to lag spikes.

Lag detection does mean your site needs to have ok performance, but when testing it, I could play 10 of 10 youtube videos all at once without a false positive.
During my tests, detection was still within the second I open dev tools, and successful if I go to the page with it already open.
I tested this in chrome, firefox, and chromium.
I also tested this on my somewhat slow website with tons of wordpress plugins installed (and the 10 videos at once test) without false positives.

== Installation ==

1. Upload plugin to the /wp-content/plugins
2. Activate the plugin through the "Plugins" menu in WordPress
3. Go to this plugins Settings and Check "Plugin Enabled" To Enable the plugin
4. Edit any other settings to your preference
5. Click Save and Enjoy

== Frequently Asked Questions ==

= Does this block users if they already have developer tools open? =
yes, developer tools is detected when the user first loads the page. If developer tools was opened on another site first, its detected by the plugin.
no matter when or where developer tools was opened, it should get blocked.

= Can admins still use Inspect Element? =
yes, this plugin detects if a user is has "manage_options" permission, and only blocks those who do not have permission.

= Does this plugin block the f12 key? =
common keys including f12 get blocked.

= Does this plugin block right click? =
There is a setting you can toggle on or off to decide if you want to block right click.

= Can Inspect Element be used on wp-login? =
the plugin attempts to block non-admin users from inspect element, including on wp-login.
If you would like to better protect wp-login, there is another plugin by SwiftNinjaPro that will only allow specific IP's to assess wp-login
https://wordpress.org/plugins/swiftninjapro-wp-login-whitelist-ip/

= Is there still a way around this plugins block? =
unfortunately, there is always a way for someone to find a work-around to any code. Opening the console is done client side, so it can only be detected client side. This plugin does make it much harder for non-admins to inspect element though.

= What do I do if I can't access the login screen? =
This could be because of the browser your using. Some browsers may respond differently.

If you cant access your site, you can do 1 of 2 things to disable this plugin:
 1. Contact your host, and ask them to disable the plugin manually
 2. Use FTP, Filezilla, or cpanel, then (click "File Manager", if using cpanel) navigate to public_html/wp-content/plugins, then find the folder "swiftninjapro-inspect-element-console-blocker" and rename it to "swiftninjapro-inspect-element-console-blocker-off" to disable the plugin.

== Screenshots ==
1. Blocking unauthorized users from Inspect Element

== Changelog ==

= 3.2.1 =
Added default option to disable plugin on 404 pages

= 3.1 =
Added option to disable on mobile browsers

= 3.0 =
Fixed many bugs
Made a new detection method thats fast and accurate
Now works in firefox

= 2.0.1 =
Added options to disable console blocker on admin and login pages (this could fix some issues)

= 2.0 =
Scripts rebuilt
Now uses devtools-detect from github

= 1.8 =
Added option to randomize the plugins javascript variable and function names

= 1.7 =
Added option to allow Search Engines for Sitemaps

= 1.6 =
Improved accuracy of devtools open detection

= 1.5.1 =
Improved how 404 page looks

= 1.5 =
Fixed detection for Microsoft Edge

= 1.4.6 =
changed display name

= 1.4 =
improved detection of opened console

= 1.3 =
added setting to allow or deny right clicks

= 1.2 =
Small change to how the plugin keeps people out of the direct url of it's own files
includes attempt to send them to 404 page
tries to make direct access look like a 404 error to trick hackers

= 1.1.2 =
Bug Fix
fixed false positive that blocked Google bots
now the plugin does not send bots, like Google PageSpeed Insights, to the 404 page

= 1.1.1 =
Bug Fix
fixed console opening detection

= 1.1 =
Improved detection of wp-login
404 screen now displays "404 error Page Not Found"

= 1.0 =
First Version

== Upgrade Notice ==

= 3.2.1 =
Added default option to disable plugin on 404 pages

= 3.1 =
Added option to disable on mobile browsers

= 3.0 =
Fixed many bugs
Made a new detection method thats fast and accurate
Now works in firefox

= 2.0.1 =
Added options to disable console blocker on admin and login pages (this could fix some issues)

= 2.0 =
Scripts rebuilt
Now uses devtools-detect from github

= 1.8 =
Added option to randomize the plugins javascript variable and function names

= 1.7 =
Added option to allow Search Engines for Sitemaps

= 1.6 =
Improved accuracy of devtools open detection

= 1.5.1 =
Improved how 404 page looks

= 1.5 =
Fixed detection for Microsoft Edge

= 1.4.6 =
changed display name

= 1.4 =
improved detection of opened console

= 1.3 =
added setting to allow or deny right clicks

= 1.2 =
Small change to how the plugin keeps people out of the direct url of it's own files
includes attempt to send them to 404 page
tries to make direct access look like a 404 error to trick hackers

= 1.1.2 =
Bug Fix
fixed false positive that blocked Google bots
now the plugin does not send bots, like Google PageSpeed Insights, to the 404 page

= 1.1.1 =
Bug Fix
fixed console opening detection

= 1.1 =
Improved detection of wp-login
404 screen now displays "404 error Page Not Found"

= 1.0 =
First Version
