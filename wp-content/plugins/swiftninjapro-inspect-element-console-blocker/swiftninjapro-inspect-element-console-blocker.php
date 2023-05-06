<?php
/**
* @package SwiftNinjaProBlockConsole
*/
/*
Plugin Name: Developer Tools Blocker
Plugin URI: https://www.swiftninjapro.com/plugins/wordpress/?plugin=swiftninjapro-inspect-element-console-blocker
Description: This plugin blocks non-admin users from using inspect element, while still allowing access those with manage_options permission.
Version: 3.2.1
Author: SwiftNinjaPro
Author URI: https://www.swiftninjapro.com
License: GPLv2 or later
Text Domain: swiftninjapro-inspect-element-console-blocker
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/* Includes devtools-detect javascript from github | https://github.com/sindresorhus/devtools-detect */

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProBlockConsole')){

  class SwiftNinjaProBlockConsole{

    public $pluginSettingsName = 'Developer Tools Blocker';
    public $pluginSettingsPermalink = 'swiftninjapro-inspect-element-console-blocker';
    public $settings_PluginName = 'InspectElementBlocker';

    public $pluginName;
    private $settings_icon;

    function __construct(){
      $this->pluginName = plugin_basename(__FILE__);
    }

    function startPlugin(){
      $pluginEnabled = $this->getSetting('Enabled');
      if(isset($pluginEnabled) && ($pluginEnabled || $pluginEnabled === false || $pluginEnabled === '')){
        $pluginEnabled = $this->trueText($pluginEnabled);
      }else{$pluginEnabled = true;}
      if($pluginEnabled){
        require_once(plugin_dir_path(__FILE__).'main.php');
        $swiftNinjaProBlockConsoleMain->start($this->settings_PluginName);
      }
    }

    function register(){
      $this->settings_icon = plugins_url('assets/settings_icon.png', __FILE__);
      add_action('admin_menu', array($this, 'add_admin_pages'));
      add_filter("plugin_action_links_$this->pluginName", array($this, 'settings_link'));
    }

    function trueText($text){
      if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
        return true;
      }else{return false;}
    }

    function getSetting($name){
      $sName = 'SwiftNinjaPro'.$this->settings_PluginName.'_'.$name;
      return strip_tags(get_option($sName));
    }

    public function settings_link($links){
      $settings_link = '<a href="admin.php?page='.$this->pluginSettingsPermalink.'">Settings</a>';
      array_push($links, $settings_link);
      return $links;
    }

    function add_admin_pages(){
      if(empty($GLOBALS['admin_page_hooks']['swiftninjapro-settings'])){
        add_menu_page('SwiftNinjaPro Settings', 'SwiftNinjaPro Settings', 'manage_options', 'swiftninjapro-settings', array($this, 'settings_index'), $this->settings_icon, 100);
      }
      $adminOnly = $this->trueText($this->getSetting('AdminOnly'));
      if($adminOnly && current_user_can('administrator')){
        add_submenu_page('swiftninjapro-settings', $this->pluginSettingsName, $this->pluginSettingsName, 'administrator', $this->pluginSettingsPermalink, array($this, 'admin_index'));
      }else if(!$adminOnly){
        add_submenu_page('swiftninjapro-settings', $this->pluginSettingsName, $this->pluginSettingsName, 'manage_options', $this->pluginSettingsPermalink, array($this, 'admin_index'));
      }
    }

    function admin_index(){
      require_once(plugin_dir_path(__FILE__).'templates/admin.php');
    }

    function settings_index(){
      require_once(plugin_dir_path(__FILE__).'templates/settings-info.php');
    }

    function activate(){
      flush_rewrite_rules();
    }

    function deactivate(){
      flush_rewrite_rules();
    }

  }

  $swiftNinjaProBlockConsole = new SwiftNinjaProBlockConsole();
  $swiftNinjaProBlockConsole->register();
  $swiftNinjaProBlockConsole->startPlugin();

  register_activation_hook(__FILE__, array($swiftNinjaProBlockConsole, 'activate'));
  register_deactivation_hook(__FILE__, array($swiftNinjaProBlockConsole, 'deactivate'));

}
