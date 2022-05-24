<?php
echo "<script>var return_url ='".esc_url_raw($this->url)."';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$class = "";
$message_p = "";
if (isset($_POST['ee_submit_plugin'])) {
    $settings = $TVC_Admin_Helper->get_ee_options_settings();
    if(!empty(sanitize_text_field($_POST['ga_id']))){
      $settings['tracking_option'] = "UA";
    }
    if(!empty(sanitize_text_field($_POST['gm_id']))){
      $settings['tracking_option'] = "GA4";
    }
    if(!empty(sanitize_text_field($_POST['gm_id'])) && !empty(sanitize_text_field($_POST['ga_id']))){
      $settings['tracking_option'] = "BOTH";
    }
    update_option('ads_tracking_id', sanitize_text_field($_POST['google_ads_id']));
    
    $settings['ga_eeT'] = isset($_POST["ga_eeT"])?sanitize_text_field($_POST["ga_eeT"]):"";
    
    $settings['ga_ST'] = isset($_POST["ga_ST"])?sanitize_text_field($_POST["ga_ST"]):"";           
    $settings['gm_id'] = isset($_POST["gm_id"])?sanitize_text_field($_POST["gm_id"]):"";
    $settings['ga_id'] = isset($_POST["ga_id"])?sanitize_text_field($_POST["ga_id"]):"";
    $settings['google_ads_id'] = isset($_POST["google_ads_id"])?sanitize_text_field($_POST["google_ads_id"]):"";
    $settings['google_merchant_id'] = isset($_POST["google_merchant_id"])?sanitize_text_field($_POST["google_merchant_id"]):"";
    $settings['ga_gUser'] = isset($_POST["ga_gUser"])?sanitize_text_field($_POST["ga_gUser"]):"";
    //$_POST['ga_gCkout'] = 'on';
    $settings['ga_Impr'] = isset($_POST["ga_Impr"])?sanitize_text_field($_POST["ga_Impr"]):"1";
    $settings['ga_IPA'] = isset($_POST["ga_IPA"])?sanitize_text_field($_POST["ga_IPA"]):"";
    //$settings['ga_OPTOUT'] = isset($_POST["ga_OPTOUT"])?sanitize_text_field($_POST["ga_OPTOUT"]):"";
    $settings['ga_PrivacyPolicy'] = isset($_POST["ga_PrivacyPolicy"])?sanitize_text_field($_POST["ga_PrivacyPolicy"]):"";
    $settings['google-analytic'] = '';
    $settings['tvc_conversion_tracking_type'] = isset($_POST["tvc_conversion_tracking_type"])?sanitize_text_field($_POST["tvc_conversion_tracking_type"]):"";
    $settings['tvc_product_detail_conversion_tracking_type'] = isset($_POST["tvc_product_detail_conversion_tracking_type"])?sanitize_text_field($_POST["tvc_product_detail_conversion_tracking_type"]):"";
    $settings['tvc_checkout_conversion_tracking_type'] = isset($_POST["tvc_checkout_conversion_tracking_type"])?sanitize_text_field($_POST["tvc_checkout_conversion_tracking_type"]):"";
    $settings['fb_pixel_id'] = isset($_POST["fb_pixel_id"])?sanitize_text_field($_POST["fb_pixel_id"]):"";
    
    $TVC_Admin_Helper->save_ee_options_settings($settings);
    $TVC_Admin_Helper->update_app_status();

    $class = 'alert-message tvc-alert-success';
    $message_p = esc_html__( 'Your settings have been saved.', 'conversios' );
}
$data = unserialize(get_option('ee_options'));

$subscription_id = $TVC_Admin_Helper->get_subscriptionId();
$TVC_Admin_Helper->add_spinner_html();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$googleDetail = "";
$plan_id = 1;
if(isset($google_detail['setting'])){
  $googleDetail = $google_detail['setting'];
  if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
    $plan_id = $googleDetail->plan_id;
  }
}
?>
<div class="tab-content">
  <?php if($message_p){
    printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message_p));
  }?>
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 border-right">
          <?php if($plan_id == 1){?>
          <div class="licence tvc-licence" >            
            <div class="tvc_licence_key_wapper <?php if($plan_id != 1){?>tvc-hide<?php }?>">
              <p><?php esc_html_e("You are using our free plugin, no licence needed ! Happy analyzing..!! :)","conversios"); ?></p>
              <p class="font-weight-bold"><?php esc_html_e("To unlock more features of google products, consider our","conversios"); ?> <a href="<?php echo esc_url_raw($TVC_Admin_Helper->get_pro_plan_site().'?utm_source=EE+Plugin+User+Interface&utm_medium=Google+Analytics+Screen+pro+version&utm_campaign=Upsell+at+Conversios'); ?>" target="_blank"><?php esc_html_e("pro version.","conversios"); ?></a></p>              
              <form method="post" name="google-analytic" id="tvc-licence-active"> 
                <div class="input-group">
                  <input type="text" id="licence_key" name="licence_key" class="form-control" placeholder="<?php esc_html_e("Already purchased? Enter licence key","conversios"); ?>" required="">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-primary" name="verify-licence-key"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/right-arrow.svg'); ?>" alt="active licence key"></button>
                  </div>
                </div>
              </form>
              
            </div>         
          </div>
        <?php }?>
          <div class="google-account-analytics">
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                  <h2 class="ga-title"><?php esc_html_e("Connected Google Analytics Account:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  (isset($data['ga_id']) && $data['ga_id'] != '') ? $data['ga_id'] : '<span>'.esc_html__("Get started","conversios").'</span>'; ?>
                  </p>
                  <?php
                  if (isset($data['ga_id']) && $data['ga_id'] != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'.esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Connected Google Analytics 4 Account:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo (isset($data['gm_id']) && $data['gm_id'] != '') ? esc_attr($data['gm_id']) : '<span>'.esc_html__("Get started","conversios").'</span>'; ?>
                  </p>
                  <?php
                  if (isset($data['gm_id']) && esc_attr($data['gm_id']) != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else { 
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
                  <p class="ga-text">
                    <?php echo  (isset($data['google_ads_id']) && $data['google_ads_id'] != '') ? esc_attr($data['google_ads_id']) : '<span>'.esc_html__("Get started","conversios").'</span>'; ?>
                  </p>
                  <?php
                  if (isset($data['google_ads_id']) && esc_attr($data['google_ads_id']) != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Linked Google Merchant Center Account:","conversios"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text">
                    <?php echo  (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') ? esc_attr($data['google_merchant_id']) : '<span>'.esc_html__("Get started","conversios").'</span>'; ?>
                  </p>
                  <?php
                  if (isset($data['google_merchant_id']) && esc_attr($data['google_merchant_id']) != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else { 
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                  }?>
                </div>
              </div>              
            </div>
          </div>

          <form id="ee_plugin_form" class="tvc_ee_plugin_form" name="google-analytic-setting-form" method="post" >
            <table class="table">
              <tbody>
                <tr>
                  <th>
                    <label class="align-middle" for="tracking_code"><?php esc_html_e("Tracking Code","conversios"); ?></label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_ST = !empty($data['ga_ST']) ? 'checked' : ''; ?>
                      <input type="checkbox"  name="ga_ST" id="ga_ST" <?php echo esc_attr($ga_ST); ?> >
                      <label class="custom-control-label" for="ga_ST"><?php esc_html_e("Add Global Site Tracking Code 'gtag.js'","conversios"); ?></label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("This feature adds new gtag.js tracking code to your store. You don't need to enable this if gtag.js is implemented via any third party analytics plugin.","conversios"); ?>"></i>
                    </label><br/>
                    <label  class = "align-middle">
                        <?php $ga_eeT = !empty($data['ga_eeT']) ? 'checked' : ''; ?>
                        <input type="checkbox"  name="ga_eeT" id="ga_eeT" <?php echo esc_attr($ga_eeT); ?> >
                        <label class="custom-control-label" for="ga_eeT"><?php esc_html_e("Add Enhanced Ecommerce Tracking Code","conversios"); ?></label>
                        
                        <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("This feature adds Enhanced Ecommerce Tracking Code to your Store","conversios"); ?>"></i>
                    </label><br/>
                    <label class = "align-middle">
                        <?php $ga_gUser = !empty($data['ga_gUser']) ? 'checked' : ''; ?>
                        <input type="checkbox"  name="ga_gUser" id="ga_gUser" <?php echo esc_attr($ga_gUser); ?> >
                        <label class="custom-control-label" for="ga_gUser"><?php esc_html_e("Add Code to Track the Login Step of Guest Users (Optional)","conversios"); ?></label>
                        
                        <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("If you have Guest Check out enable, we recommend you to add this code","conversios"); ?>"></i>
                    </label>  
                  </td>
                </tr>
                <tr>
                  <th>
                    <label for="ga_Impr"><?php esc_html_e("Impression Thresold","conversios"); ?></label>
                  </th>
                  <td>
                    <?php $ga_Impr = !empty($data['ga_Impr']) ? $data['ga_Impr'] : 6; ?>
                    <input type="number" min="1" id="ga_Impr"  name = "ga_Impr" value = "<?php echo esc_attr($ga_Impr); ?>">
                    <label for="ga_Impr"></label>
                    <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("This feature sets Impression threshold for category page. It sends hit after these many numbers of products impressions.","conversios"); ?>"></i>
                    <p class="description"><b><?php esc_html_e("Note : To avoid processing load on server we recommend upto 6 Impression Thresold.","conversios"); ?></b></p>
                  </td>
                </tr>
                <tr>
                  <th>
                    <label class = "align-middle" for="ga_IPA"><?php esc_html_e("I.P. Anoymization","conversios"); ?></label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_IPA = !empty($data['ga_IPA']) ? 'checked' : ''; ?>
                      <input class="" type="checkbox" name="ga_IPA" id="ga_IPA"  <?php echo esc_attr($ga_IPA); ?>>
                      <label class="custom-control-label" for="ga_IPA"><?php esc_html_e("Enable I.P. Anonymization","conversios"); ?></label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance","conversios"); ?>"></i>
                    </label>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="padding: 0.5rem 0;">
                    <strong>Tracking Trigger Point Settings</strong>
                    <table class="tracking-trigger">
                      <tr>
                        <th style="padding: 0 1rem;">
                          <label class = "align-middle" for="tvc_product_detail_conversion_tracking_type"><?php esc_html_e("AddtoCart PDP","conversios"); ?></label>
                        </th>
                        <td>
                          <?php $tvc_prod_dtl_con_tracking_type = isset($data['tvc_product_detail_conversion_tracking_type'])?$data['tvc_product_detail_conversion_tracking_type']:""; ?>
                            <select name="tvc_product_detail_conversion_tracking_type" id="tvc_conversion_tracking_type">
                              <option value="woo-product-detail-hook" <?php echo ($tvc_prod_dtl_con_tracking_type != "on-productdetail-page")?"selected":""; ?>><?php esc_html_e("hook - woocommerce_after_single_product","conversios"); ?></option>
                              <option value="on-productdetail-page" <?php echo ($tvc_prod_dtl_con_tracking_type == "on-productdetail-page")?"selected":""; ?>><?php esc_html_e("On Page Condition","conversios"); ?></option>
                            </select>
                            <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("If your AddtoCart event (product detail page) tracking does not work with the current tracking method then switch to another method.","conversios"); ?>"></i>
                        </td>
                      </tr>
                      <tr>
                        <th style="padding: 0 1rem;">
                          <label class = "align-middle" for="tvc_checkout_conversion_tracking_type"><?php esc_html_e("Checkout Steps","conversios"); ?></label>
                        </th>
                        <td>
                          <?php $tvc_checkout_con_tracking_type = isset($data['tvc_checkout_conversion_tracking_type'])?$data['tvc_checkout_conversion_tracking_type']:""; ?>
                            <select name="tvc_checkout_conversion_tracking_type" id="tvc_conversion_tracking_type">
                              <option value="woo-checkout-hook" <?php echo ($tvc_checkout_con_tracking_type != "on-checkout-page")?"selected":""; ?>><?php esc_html_e("hook - woocommerce_before_checkout_form","conversios"); ?></option>
                              <option value="on-checkout-page" <?php echo ($tvc_checkout_con_tracking_type == "on-checkout-page")?"selected":""; ?>><?php esc_html_e("On Page Condition","conversios"); ?></option>
                            </select>
                            <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("If your checkout page tracking does not work with the current tracking method then switch to another method.","conversios"); ?>"></i>
                        </td>
                      </tr>

                      <tr>
                        <th style="padding: 0 1rem;">
                          <label class = "align-middle" for="tvc_conversion_tracking_type"><?php esc_html_e("Order Conversion","conversios"); ?></label>
                        </th>
                        <td>
                          <?php $tvc_con_tracking_type = isset($data['tvc_conversion_tracking_type'])?$data['tvc_conversion_tracking_type']:""; ?>
                            <select name="tvc_conversion_tracking_type" id="tvc_conversion_tracking_type">
                              <option value="woo-thankyou-hook" <?php echo ($tvc_con_tracking_type != "on-thankyou-page")?"selected":""; ?>><?php esc_html_e("hook - woocommerce_thankyou","conversios"); ?></option>
                              <option value="on-thankyou-page" <?php echo ($tvc_con_tracking_type == "on-thankyou-page")?"selected":""; ?>><?php esc_html_e("On Page Condition","conversios"); ?></option>
                            </select>
                            <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("If your thank you page conversion tracking does not work with the current tracking method then switch to another method.","conversios"); ?>"></i>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <?php /*
                <tr>
                  <th>
                      <label class = "align-middle" for="ga_OPTOUT"><?php esc_html_e("Google Analytics Opt Out","conversios"); ?></label>
                  </th>
                  <td>
                    <label  class = "align-middle">
                      <?php $ga_OPTOUT = !empty($data['ga_OPTOUT']) ? 'checked' : ''; ?>
                      <input class="" type="checkbox" name="ga_OPTOUT" id="ga_OPTOUT"  <?php echo esc_attr($ga_OPTOUT); ?>>
                      <label class="custom-control-label" for="ga_OPTOUT"><?php esc_html_e("Enable Google Analytics Opt Out (Optional)","conversios"); ?></label>
                      
                      <i style="cursor: help;" class="fas fa-question-circle" title="<?php esc_html_e("Use this feature to provide website visitors the ability to prevent their data from being used by Google Analytics As per the GDPR compliance.Go through the documentation to check the setup","conversios"); ?>"></i>
                    </label>
                  </td>
                </tr> */ ?>
                <tr>
                    <th>
                      <img style="height: 20px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/fb-icon.png"); ?>" > Facebook pixel ID 
                    </th>
                    <td>
                      <?php $fb_pixel_id = isset($data['fb_pixel_id'])?$data['fb_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled" name="fb_pixel_id" id="fb_pixel_id" value="<?php echo esc_attr($fb_pixel_id); ?>">
                      <small>The Facebook pixel ID looks like. 518896233175751</small>
                    </td>
                </tr>
                <tr>
                  <th>
                    <label class = "align-middle" for="ga_PrivacyPolicy"><?php esc_html_e("Privacy Policy","conversios"); ?></label>
                  </th>
                  <td>
                    <label class = "align-middle">
                      <?php $ga_PrivacyPolicy = !empty($data['ga_PrivacyPolicy']) ? 'checked' : ''; ?>
                      <input type="checkbox" name="ga_PrivacyPolicy" id="ga_PrivacyPolicy" required="required" <?php echo esc_attr($ga_PrivacyPolicy); ?>>
                      <label class="custom-control-label" for="ga_PrivacyPolicy"><?php esc_html_e("Accept Privacy Policy of Plugin","conversios"); ?></label>
                      
                      <p class="description"><?php esc_html_e("By using Tatvic Plugin, you agree to Tatvic plugin's","conversios"); ?> <a href= "<?php echo esc_url_raw("https://www.tatvic.com/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woo_premium_plugin&utm_campaign=GDPR_complaince_ecomm_plugins"); ?>" target="_blank"><?php esc_html_e("Privacy Policy","conversios"); ?></a></p>
                    </label>
                  </td>
                </tr>
              </tbody>
            </table>
            <p class="submit save-for-later" id="save-for-later">
              <input type="hidden" id="ga_id" name = "ga_id" value="<?= esc_attr((!empty($data['ga_id']))?$data['ga_id']:""); ?>"/>
              <input type="hidden" id="gm_id" name = "gm_id" value="<?= esc_attr((!empty($data['gm_id']))?$data['gm_id']:""); ?>"/>
              <input type="hidden" id="google_ads_id" name = "google_ads_id" value="<?= esc_attr((!empty($data['google_ads_id']))?$data['google_ads_id']:""); ?>"/>
              <input type="hidden" id="google_merchant_id" name = "google_merchant_id" value="<?= esc_attr((!empty($data['google_merchant_id']))?$data['google_merchant_id']:""); ?>"/>
              <input type="hidden" name="subscription_id" value="<?php echo esc_attr((!empty($data['subscription_id']))?$data['subscription_id']:""); ?>">
              <button type="submit"  class="btn btn-primary" id="ee_submit_plugin" name="ee_submit_plugin"><?php esc_html_e("Save","conversios"); ?></button>
          </p>
          </form>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_google_ga_sidebar(); ?>
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
  echo get_connect_google_popup_html_to_active_licence();
?>
<script>
jQuery(document).ready(function () {
  /*facebook*/
  jQuery("#fb_pixel_id").keypress(function (evt){
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[-\d\.]/; // dowolna liczba (+- ,.) :)
    var objRegex = /^-?\d*[\.]?\d*$/;
    var val = $(evt.target).val();
    if(!regex.test(key) || !objRegex.test(val+key) || 
            !theEvent.keyCode == 46 || !theEvent.keyCode == 8) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    };
  });

  jQuery(document).on('click','#tvc_google_connect_active_licence_close',function(event){
    jQuery('#tvc_google_connect_active_licence').modal('hide');
  });
  jQuery(document).on('click','.tvc_licence_key_change',function(event){
    jQuery(".tvc_licence_key_change_wapper").slideUp(500);
    jQuery(".tvc_licence_key_wapper").slideDown(700);
  });
  jQuery(document).on('submit','form#tvc-licence-active',function(event){
    event.preventDefault();
    let licence_key = jQuery("#licence_key").val();
    var form_data = jQuery("#tvc-licence-active").serialize();
    if(licence_key!=""){
      var data = {
        action: "tvc_call_active_licence",
        licence_key:licence_key        
      };
      $.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: data,
        beforeSend: function(){
          tvc_helper.loaderSection(true);
        },
        success: function(response){
          if (response.error === false) {          
            tvc_helper.tvc_alert("success","",response.message);
            setTimeout(function(){ 
              location.reload();
            }, 2000);
          }else{
            if( response.is_connect == false){    
              jQuery('#tvc_google_connect_active_licence').modal('show');          
            }else{
              tvc_helper.tvc_alert("error","",response.message);
            }
          }
          tvc_helper.loaderSection(false);
        }
      });
    }else{
      tvc_helper.tvc_alert("error","Licence key is required.");
    }
  });
});
</script>