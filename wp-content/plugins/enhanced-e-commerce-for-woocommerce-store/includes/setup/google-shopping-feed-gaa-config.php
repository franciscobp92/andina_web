<?php
class GAAConfiguration {
  protected $TVC_Admin_Helper;
  protected $subscriptionId;
  protected $TVCProductSyncHelper;
  public function __construct() {
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->TVCProductSyncHelper = new TVCProductSyncHelper();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->site_url = "admin.php?page=conversios-google-shopping-feed&tab=";     
    $this->url = $this->TVC_Admin_Helper->get_onboarding_page_url();     
    $this->html_run();
  }
  public function includes() {
    if (!class_exists('Tatvic_Category_Wrapper')) {
      require_once(__DIR__ . '/class-tvc-product-sync-helper.php');
    }
  }

  public function html_run() {
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function create_form() {
    if(isset($_GET['welcome_msg']) && sanitize_textarea_field($_GET['welcome_msg']) == true){
      $class = 'notice notice-success';
      $message = esc_html__("Get your WooCommerce products in front of the millions of shoppers across Google by setting up your Google Merchant Center account from below.","conversios");
      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
      ?>
      <script>
        jQuery(document).ready(function() {
          var msg="<?php echo esc_html($message);?>"
          tvc_helper.tvc_alert("success","Hey!",msg,true);
        });
      </script>
      <?php
    }
    $category_wrapper_obj = new Tatvic_Category_Wrapper();
    $category_wrapper = $category_wrapper_obj->category_table_content('mapping');
    $googleDetail = [];
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }?>
<div class="tab-content">
	<div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 edit-section">
          <div class="configuration-section" id="config-pt1">
            <?php echo get_google_shopping_tabs_html($this->site_url,(isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:""); ?>
          </div>
          <div class="mt-3" id="config-pt2">
            <div class="google-account-analytics" id="gaa-config">
              <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Connected Google Merchant center account:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text"><?php echo ((isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != '') ? esc_attr($googleDetail->google_merchant_center_id) : '<span>'.esc_html__('Get started','conversios').'</span>'); ?></p>
                  <?php
                    if(isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != ''){
                      echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'.esc_url_raw( ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                    }else{
                      echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                    }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Linked Google Ads Account:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text"><?php echo (isset($googleDetail->google_ads_id) && esc_attr($googleDetail->google_ads_id) != '' ? esc_attr($googleDetail->google_ads_id) : '<span>'.esc_html__('Get started','conversios').'</span>');?></p>
                  <?php
                  if (isset($googleDetail->google_ads_id) && esc_attr($googleDetail->google_ads_id) != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else {
                    echo '<p class="ga-text text-right"><a href="' .esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                  } ?>
                </div>
              </div>
            </div>
            <?php
            if (isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != '') {?>
            <div class="row mb-3">
              <div class="col-6 col-md-4">
                <h2 class="ga-title"><?php esc_html_e("Sync Products:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-4">
                <button id="tvc_btn_product_sync" type="button" class="btn btn-primary btn-success" data-bs-toggle="modal" data-bs-target="#syncProduct"><?php esc_html_e("Sync New Products","conversios"); ?></button>                        
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-4">
                <h2 class="ga-title"><?php esc_html_e("Smart Shopping Campaigns:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
                <a href="admin.php?page=conversios-google-shopping-feed&tab=add_campaign_page" class="btn btn-primary btn-success"><?php esc_html_e("Create Smart Shopping Campaign","conversios"); ?></a>
              </div>
            </div>
            <?php }else{ ?>
            <div class="row mb-3">
              <div class="col-6 col-md-4">
                <h2 class="ga-title"><?php esc_html_e("Sync Products:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-4">
              <a href="<?php echo esc_url_raw($this->url); ?>" class="btn btn-primary btn-success"><?php esc_html_e("Sync New Products","conversios"); ?></a>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-4">
                <h2 class="ga-title"><?php esc_html_e("Smart Shopping Campaigns:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
                <a href="<?php echo esc_url_raw($this->url); ?>" class="btn btn-primary btn-success"><?php esc_html_e("Create Smart Shopping Campaign","conversios"); ?></a>
              </div>
            </div>
            <?php } ?>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_help_html(); ?>
          <div class="tvc-youtube-video">
            <span>Video tutorial:</span>
            <a href="https://www.youtube.com/watch?v=FAV4mybKogg" target="_blank">Walkthrough about Onboarding</a>
            <a href="https://www.youtube.com/watch?v=4pb-oPWHb-8" target="_blank">Walkthrough about Product Sync</a>
            <a href="https://www.youtube.com/watch?v=_C9cemX6jCM" target="_blank">Walkthrough about Smart Shopping Campaign</a>
          </div> 
        </div>
      </div>
    </div>
	</div>
</div>
<?php 
// add product sync popup
echo $this->TVCProductSyncHelper->tvc_product_sync_popup_html();
?>
<?php  
$is_need_to_domain_claim = false;
if(isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) && esc_attr($this->subscriptionId) != "" && isset($googleDetail->is_domain_claim) && esc_attr($googleDetail->is_domain_claim) == '0'){
  $is_need_to_domain_claim = true;
}?>
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery(document).on("click", "#tvc_btn_product_sync", function(event){
      var is_need_to_domain_claim = "<?php echo esc_attr($is_need_to_domain_claim); ?>";
      if(is_need_to_domain_claim == 1 || is_need_to_domain_claim == true){
        event.preventDefault();
        jQuery.post(tvc_ajax_url,{
          action: "tvc_call_domain_claim"
        },function( response ){
          
        });
      }
    }); 
  });
</script>
  <?php 
  } //create_form
} ?>