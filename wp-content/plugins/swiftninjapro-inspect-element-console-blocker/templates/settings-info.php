<?php
if (!defined('ABSPATH') || !current_user_can('manage_options')) {
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}
?>

<h1>SwiftNinjaPro Settings</h1>

<h3>Any plugins by SwiftNinjaPro will be added to this settings tab to reduce the space taken up on your admin bar :D</h3>

<h2><a href="<?php echo esc_url(get_admin_url()); ?>plugin-install.php?s=swiftninjapro&tab=search&type=author">Other Plugins By SwiftNinjaPro</a></h2>

<h2><a href="https://buymeacoffee.swiftninjapro.com">Buy Me A Coffee</a></h2>
