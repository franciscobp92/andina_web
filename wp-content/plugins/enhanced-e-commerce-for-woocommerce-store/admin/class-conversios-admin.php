<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Tatvic
 */
if ( ! class_exists( 'Conversios_Admin' ) ) {
  class Conversios_Admin extends TVC_Admin_Helper {
    protected $google_detail;
    protected $url;
    protected $version;
    public function __construct() { 
      $this->version = PLUGIN_TVC_VERSION;
      $this->includes();
      $this->url = $this->get_onboarding_page_url(); // use in setting page
      $this->google_detail = $this->get_ee_options_data();
      add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
      add_action('admin_init',array($this, 'init'));     
    }
    public function includes() {
      if (!class_exists('Conversios_Header')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/class-conversios-header.php');
      }
      if (!class_exists('Conversios_Footer')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/class-conversios-footer.php');
      }   
    }

    public function init(){
      add_action( 'admin_enqueue_scripts', array($this,'enqueue_styles'));
      add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts'));
    }
        
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    4.1.4
     */
    public function enqueue_styles() {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios'  || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'conversios') !== false) ) {
        //developres hook to custom css
        do_action('add_conversios_css_'.sanitize_text_field($_GET['page']));
        //conversios page css
        if(sanitize_text_field($_GET['page']) == "conversios"){
          wp_register_style('conversios-slick-css', esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/css/slick.css') );
          wp_enqueue_style('conversios-slick-css');
          wp_register_style('conversios-daterangepicker-css', esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/css/daterangepicker.css') );
          wp_enqueue_style('conversios-daterangepicker-css');
        }
        //all conversios page css 
        wp_enqueue_style('conversios-style-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/style.css'), array(), esc_attr($this->version), 'all' );
        wp_enqueue_style('conversios-responsive-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/responsive.css'), array(), esc_attr($this->version), 'all');
      }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    4.1.4
     */
    public function enqueue_scripts() {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios'  || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'conversios') !== false) ) {
        if(sanitize_text_field($_GET['page']) == "conversios"){
          
          wp_enqueue_script( 'conversios-chart-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chart.js') );
          wp_enqueue_script( 'conversios-chart-datalabels-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chartjs-plugin-datalabels.js') );
          wp_enqueue_script( 'conversios-basictable-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/jquery.basictable.min.js') );
          wp_enqueue_script( 'conversios-moment-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/moment.min.js') );
          wp_enqueue_script( 'conversios-daterangepicker-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/daterangepicker.js') ); 

          wp_enqueue_script( 'tvc-ee-custom-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/tvc-ee-custom.js'), array( 'jquery' ), esc_attr($this->version), false );       
        }
      }
    }

    /**
     * Display Admin Page.
     *
     * @since    4.1.4
     */
    public function add_admin_pages() {  
      $google_detail = $this->google_detail;
      $plan_id = 1;
      if(isset($google_detail['setting'])){
        $googleDetail = $google_detail['setting'];
        if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
          $plan_id = $googleDetail->plan_id;
        }
      }  

      add_menu_page(
          esc_html__('Tatvic EE Plugin','conversios'), esc_html__('Tatvic EE Plugin','conversios'), 'manage_options', "conversios", array($this, 'showPage'), esc_url_raw(plugin_dir_url(__FILE__) . 'images/tatvic_logo.png'), 26
      );
      if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_submenu_page(
          'conversios', 
          esc_html__('Dashboard','conversios'), 
          esc_html__('Dashboard','conversios'), 
          'manage_options', 
          'conversios' );
        add_submenu_page(
            'conversios',
            esc_html__('Google Shopping', 'conversios'),
            esc_html__('Google Shopping', 'conversios'),
            'manage_options',
            'conversios-google-shopping-feed',
            array($this, 'showPage')
        );
        add_submenu_page(
            'conversios',
            esc_html__('Google Ads', 'conversios'),
            esc_html__('Google Ads', 'conversios'),
            'manage_options',
            'conversios-google-ads',
            array($this, 'showPage')
        );
        add_submenu_page(
          'conversios',
          esc_html__('Account Settings', 'conversios'),
          esc_html__('Account Settings', 'conversios'),
          'manage_options',
          'conversios-google-analytics',
          array($this, 'showPage')
        );
        
        add_submenu_page(
          'conversios',
          esc_html__('Account Summary', 'conversios'),
          esc_html__('Account Summary', 'conversios'),
          'manage_options',
          'conversios-account',
          array($this, 'showPage')
        );
        if($plan_id == 1){
          add_submenu_page(
            'conversios',
            esc_html__('Free Vs Pro', 'conversios'),
            esc_html__('Free Vs Pro', 'conversios'),
            'manage_options',
            'conversios-pricings',
            array($this, 'showPage')
          );
        } 
      }
    }
    
    /**
     * Display page.
     *
     * @since    4.1.4
     */
    public function showPage() {
      do_action('add_conversios_header');
      if (!empty(sanitize_text_field($_GET['page']))) {
        $get_action = str_replace("-", "_", sanitize_text_field($_GET['page']));
      } else {
        $get_action = "conversios";
      }
      if (method_exists($this, $get_action)) {
        $this->$get_action();
      }
      echo $this->get_tvc_popup_message();
      do_action('add_conversios_footer');
    }

    public function conversios(){
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-conversios-dashboard.php');
    }

    public function conversios_pricings(){
      require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/pricings.php');
      new TVC_Pricings();
    }
    public function conversios_account(){
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/account.php');
      new TVC_Account();
    }
    public function conversios_google_analytics() {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');      
        require_once( 'partials/general-fields.php');
    }
    public function conversios_google_ads() {        
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-ads.php');
        new GoogleAds();
    }
    public function conversios_google_shopping_feed() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed.php');
        $action_tab = (isset($_GET['tab']))?sanitize_text_field($_GET['tab']):"";
        if($action_tab!=""){
          $this->$action_tab();
        }else{
          new GoogleShoppingFeed();
        }
    }
    public function gaa_config_page() {        
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-gaa-config.php'); 
        new GAAConfiguration();
    }    
    public function sync_product_page() {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-sync-product.php');
      new SyncProductConfiguration();
    }
    public function shopping_campaigns_page() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-shopping-campaigns.php');
        new CampaignsConfiguration();
    }
    public function add_campaign_page() {
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/add-campaign.php');
        new AddCampaign();
    } 
    
  }
}
new Conversios_Admin();