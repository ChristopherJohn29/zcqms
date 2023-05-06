<?php
/**
* @package SwiftNinjaProBlockConsole
*/

if(!defined('ABSPATH')){
  echo '<meta http-equiv="refresh" content="0; url=/404">';
  die('404 Page Not Found');
}

if(!class_exists('SwiftNinjaProBlockConsoleMain')){

  class SwiftNinjaProBlockConsoleMain{

    private $ver = '3.0';

    public $pluginSettingsName;

    private $currentUrl;

    function start($pluginSettingsName){
      $this->pluginSettingsName = $pluginSettingsName;
      add_action('after_setup_theme', array($this, 'add_js'));
    }

    function add_js(){

      $optionDisableOn404 = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_DisableOn404')), true);
      if($optionDisableOn404 && is_404()){
        return;
      }

      $optionDisableOnMobile = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_DisableOnMobile')), true);
      if($optionDisableOnMobile && wp_is_mobile()){
        return;
      }


      $this->currentUrl = esc_url('http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.sanitize_text_field($_SERVER['HTTP_HOST']).sanitize_text_field($_SERVER['REQUEST_URI']));
      $canUseDevTool = false;
      if(current_user_can('manage_options')){
        $canUseDevTool = true;
      }

      $userIP = esc_html(strip_tags(htmlentities($_SERVER['REMOTE_ADDR'])));
      $localHost = array('127.0.0.1', '::1', 'localhost');

      $allowSearchEngine = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_AllowSearchEngine')));
      if($allowSearchEngine){
        if($this->is_search_engine() && !$this->is_proxy()){
          $canUseDevTool = true;
        }
      }else if(!$this->is_proxy() && in_array($userIP, $localHost)){
        $canUseDevTool = true;
      }

      if(!$canUseDevTool){
      
        $optionDisableOnAdmin = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_DisableOnAdmin')));
        if($optionDisableOnAdmin && is_admin()){
          return;
        }

        if($this->is_login_url()){

          $optionDisableOnLogin = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_DisableOnLogin')));
          if($optionDisableOnLogin){
            return;
          }

          $optionForceJS = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_ForceJS')));
          if($optionForceJS){
            ?>
            <noscript>
              <meta http-equiv="refresh" content="0; url=/404">
            </noscript>
            <?php
          }

          wp_enqueue_script('swiftNinjaProBlockConsoleDevtoolsDetect', plugins_url('/assets/devtools-detect.js', __FILE__), array(), '3.0.1', false);
          wp_enqueue_script('swiftNinjaProBlockConsoleBlockConsole', plugins_url('/assets/block-console.js', __FILE__), array(), $this->ver, false);

          wp_enqueue_script('swiftNinjaProBlockConsoleBlockKeys', plugins_url('/assets/block-keys.js', __FILE__), array(), $this->ver, false);

          $RightClickEnabled = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_BlockRightClick')));
          if($RightClickEnabled){
            wp_enqueue_script('swiftNinjaProBlockConsoleBlockRightClick', plugins_url('/assets/block-right-click.js', __FILE__), array(), $this->ver, false);
          }
        }else{
          add_action('wp_enqueue_scripts', array($this, 'enqueue_404_script'));
        }
      }
    }

    function enqueue_404_script(){
      if(!is_404()){

        $optionForceJS = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_ForceJS')));
        if($optionForceJS){
          ?>
          <noscript>
            <meta http-equiv="refresh" content="0; url=/404">
          </noscript>
          <?php
        }

        wp_enqueue_script('swiftNinjaProBlockConsoleDevtoolsDetect', plugins_url('/assets/devtools-detect.js', __FILE__), array(), $this->ver, false);
        wp_enqueue_script('swiftNinjaProBlockConsoleBlockConsole', plugins_url('/assets/block-console.js', __FILE__), array(), $this->ver, false);

        wp_enqueue_script('swiftNinjaProBlockConsoleBlockKeys', plugins_url('/assets/block-keys.js', __FILE__), array(), $this->ver, false);

        $RightClickEnabled = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_BlockRightClick')));
        if($RightClickEnabled){
          wp_enqueue_script('swiftNinjaProBlockConsoleBlockRightClick', plugins_url('/assets/block-right-click.js', __FILE__), array(), $this->ver, false);
        }
      }else{
        $optionSecure404 = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_Secure404')));
        if($optionSecure404){

          $optionForceJS = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_ForceJS')));
          if($optionForceJS){
            ?>
            <noscript>
              Error: Javascript Must Be Enabled
            </noscript>
            <?php
          }

          wp_enqueue_script('swiftNinjaProBlockConsoleDevtoolsDetect', plugins_url('/assets/devtools-detect.js', __FILE__), array(), '3.0.1', false);
          wp_enqueue_script('swiftNinjaProBlockConsoleClear404', plugins_url('/assets/clear-404.js', __FILE__), array(), $this->ver, false);

          wp_enqueue_script('swiftNinjaProBlockConsoleBlockKeys', plugins_url('/assets/block-keys.js', __FILE__), array(), $this->ver, false);

          $RightClickEnabled = $this->TrueText(strip_tags(get_option('SwiftNinjaProInspectElementBlocker_BlockRightClick')));
          if($RightClickEnabled){
            wp_enqueue_script('swiftNinjaProBlockConsoleBlockRightClick', plugins_url('/assets/block-right-click.js', __FILE__), array(), $this->ver, false);
          }
        }
      }
    }

    function is_login_url(){
      if($this->currentUrl == wp_login_url() || $this->currentUrl == wp_registration_url() || $this->currentUrl == wp_lostpassword_url() || $this->currentUrl == wp_logout_url()){
        return true;
      }else{return false;}
    }

    function get_bot(){
      $userAgent = esc_html(strip_tags($_SERVER['HTTP_USER_AGENT']));
      if(isset($userAgent) && $userAgent && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $userAgent)){
        return $userAgent;
      }else{return false;}
    }

    function is_search_engine(){
      $bot = $this->get_bot();
      if(!$bot){return false;}
      $crawlers = array(
        'Google' => 'Google',
        'MSN' => 'msnbot',
        'Rambler' => 'Rambler',
        'Yahoo' => 'Yahoo',
        'AbachoBOT' => 'AbachoBOT',
        'accoona' => 'Accoona',
        'AcoiRobot' => 'AcoiRobot',
        'ASPSeek' => 'ASPSeek',
        'CrocCrawler' => 'CrocCrawler',
        'Dumbot' => 'Dumbot',
        'FAST-WebCrawler' => 'FAST-WebCrawler',
        'GeonaBot' => 'GeonaBot',
        'Gigabot' => 'Gigabot',
        'Lycos spider' => 'Lycos',
        'MSRBOT' => 'MSRBOT',
        'Altavista robot' => 'Scooter',
        'AltaVista robot' => 'Altavista',
        'ID-Search Bot' => 'IDBot',
        'eStyle Bot' => 'eStyle',
        'Scrubby robot' => 'Scrubby',
        'Facebook' => 'facebookexternalhit',
      );
      $crawlers_agents = implode('|', $crawlers);
      if(strpos($crawlers_agents, $bot) !== false){
        return true;
      }else{return false;}
    }

    function is_proxy(){
      if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){return true;}
      if(esc_html(strip_tags($_SERVER['REMOTE_ADDR'])) !== esc_html(strip_tags($_SERVER['HTTP_CLIENT_IP']))){return true;}
      return false;
    }

    function TrueText($text, $default = false){
      if(!isset($text) || $text === null){
        return $default;
      }else if($text === 'true' || $text === 'TRUE' || $text === 'True' || $text === true || $text === 1 || $text === 'on'){
        return true;
      }else if ($text === 'false' || $text === 'FALSE' || $text === 'False' || $text === false || $text === 0 || $text === 'off'){
        return false;
      }else{return $default;}
    }

  }

  $swiftNinjaProBlockConsoleMain = new SwiftNinjaProBlockConsoleMain();

}
