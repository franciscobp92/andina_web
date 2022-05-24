<?php

/**
* TVC Ajax File Class.
*
* @package TVC Product Feed Manager/Data/Classes
*/
if(!defined('ABSPATH')){
exit;
}

if(!class_exists('TVC_Ajax_File')) :
/**
 * Ajax File Class
 */
class TVC_Ajax_File extends TVC_Ajax_Calls {
  private $apiDomain;
  protected $access_token;
  protected $refresh_token;
  public function __construct(){
    parent::__construct();
    $this->apiDomain = TVC_API_CALL_URL;
    // hooks
    add_action('wp_ajax_tvcajax-get-campaign-categories', array($this, 'tvcajax_get_campaign_categories'));
    add_action('wp_ajax_tvcajax-update-campaign-status', array($this, 'tvcajax_update_campaign_status'));
    add_action('wp_ajax_tvcajax-delete-campaign', array($this, 'tvcajax_delete_campaign'));
    
    add_action('wp_ajax_tvcajax-gmc-category-lists', array($this, 'tvcajax_get_gmc_categories'));
    //add_action('wp_ajax_tvcajax-custom-metrics-dimension', array($this, 'tvcajax_custom_metrics_dimension'));
    add_action('wp_ajax_tvcajax-store-time-taken', array($this, 'tvcajax_store_time_taken'));

    add_action('wp_ajax_tvc_call_api_sync', array($this, 'tvc_call_api_sync'));
    add_action('wp_ajax_tvc_call_import_gmc_product', array($this, 'tvc_call_import_gmc_product'));
    add_action('wp_ajax_tvc_call_domain_claim', array($this, 'tvc_call_domain_claim'));
    add_action('wp_ajax_tvc_call_site_verified', array($this, 'tvc_call_site_verified'));
    add_action('wp_ajax_tvc_call_notice_dismiss', array($this, 'tvc_call_notice_dismiss'));
    add_action('wp_ajax_tvc_call_notification_dismiss', array($this, 'tvc_call_notification_dismiss'));
    add_action('wp_ajax_tvc_call_active_licence', array($this, 'tvc_call_active_licence'));
    add_action('wp_ajax_tvc_call_add_survey', array($this, 'tvc_call_add_survey'));

    add_action('wp_ajax_tvc_call_add_customer_feedback', array($this, 'tvc_call_add_customer_feedback'));

    add_action('wp_ajax_tvcajax_product_sync_bantch_wise', array($this, 'tvcajax_product_sync_bantch_wise'));
  }

  public function tvcajax_product_sync_bantch_wise(){
    global $wpdb;
    $rs = array();
    // barch size for inser data in DB
    $product_db_batch_size = 100;
    // barch size for inser product in GMC
    //$product_batch_size = 25;
    $product_batch_size = isset($_POST['product_batch_size'])?sanitize_text_field($_POST['product_batch_size']):"25";
    if(!class_exists('CustomApi')){
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
    }
    if(!class_exists('TVCProductSyncHelper')){
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
    }
    $customObj = new CustomApi();
    $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $TVCProductSyncHelper = new TVCProductSyncHelper();
    //sleep(3);
    $prouct_pre_sync_table = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
    
    $sync_produt = ""; $sync_produt_p = ""; $is_synced_up = ""; $sync_message = "";
    $sync_progressive_data = isset($_POST['sync_progressive_data'])?$_POST['sync_progressive_data']:"";

    $sync_produt = isset($sync_progressive_data['sync_produt'])?sanitize_text_field($sync_progressive_data['sync_produt']):"";
    $sync_produt = sanitize_text_field($sync_produt);

    $sync_step = isset($sync_progressive_data['sync_step'])?sanitize_text_field($sync_progressive_data['sync_step']):"1";
    $sync_step = sanitize_text_field($sync_step);

    $total_product =isset($sync_progressive_data['total_product'])?sanitize_text_field($sync_progressive_data['total_product']):"0";
    $total_product = sanitize_text_field($total_product);

    $last_sync_product_id =isset($sync_progressive_data['last_sync_product_id'])?$sync_progressive_data['last_sync_product_id']:"";
    $last_sync_product_id = sanitize_text_field( intval( $last_sync_product_id ) );

    $skip_products =isset($sync_progressive_data['skip_products'])?sanitize_text_field($sync_progressive_data['skip_products']):"0";
    $skip_products = sanitize_text_field($skip_products);

    $account_id = isset($_POST['account_id'])?sanitize_text_field($_POST['account_id']):"";
    $customer_id = isset($_POST['customer_id'])?sanitize_text_field($_POST['customer_id']):"";
    $subscription_id = isset($_POST['subscription_id'])?sanitize_text_field($_POST['subscription_id']):"";
    $data = isset($_POST['tvc_data'])?$_POST['tvc_data']:"";
    parse_str($data, $formArray);    
    if(!empty($formArray)){
      foreach ($formArray as $key => $value) {
        $formArray[$key] = sanitize_text_field($value);
      }
    }
    if( $sync_progressive_data == "" && $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data") > 0 ){
      $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
    }
    /*
     * step one start
     */
    if($total_product <= $sync_produt && $sync_step == 1){
      $sync_step = 2;
      $sync_produt = 0;
    }
    if($sync_step == 1){
      //parse_str($data, $formArray);      
      $mappedCatsDB = [];
      $mappedCats = [];
      $mappedAttrs = [];
      $skipProducts = [];
      foreach($formArray as $key => $value){
        if(preg_match("/^category-name-/i", $key)){
          if($value != ''){
            $keyArray = explode("name-", $key);
            $mappedCatsDB[$keyArray[1]]['name'] = $value;
          }
          unset($formArray[$key]);
        }else if(preg_match("/^category-/i", $key)){
          if($value != '' && $value > 0){
            $keyArray = explode("-", $key);
            $mappedCats[$keyArray[1]] = $value;
            $mappedCatsDB[$keyArray[1]]['id'] = $value;
          }
          unset($formArray[$key]);
        }else{
          if($value){
              $mappedAttrs[$key] = $value;
          }
        }
      }
      //add/update data in defoult profile
      $profile_data = array("profile_title"=>esc_sql("Default"),"g_attribute_mapping"=>json_encode($mappedAttrs),"update_date"=>date('Y-m-d'));
      if($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") ==0){
        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s","%s"));
      }else{
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
      }
      update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
      update_option("ee_prod_mapped_attrs", serialize($mappedAttrs)); 

      /*
       * start product add in DB
       * start clategory list
       */
      if(!empty($mappedCats)){
        $batch_count =0; 
        $values = array();
        $place_holders = array();
        foreach($mappedCats as $mc_key => $mappedCat){
          $all_products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
              array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $mc_key, /* category name */
                'operator' => 'IN',
                'include_children' => false
              )
            )
          ));
          /*
           * start product list , it's run per category
           */
          if(!empty($all_products)){
            foreach($all_products as $postkey => $postvalue){
              $batch_count++;        
              array_push( $values, esc_sql($postvalue->ID), esc_sql($mc_key), esc_sql($mappedCat), 1, date('Y-m-d') );
              $place_holders[] = "('%d', '%d', '%d','%d', '%s')";
              if($batch_count >= $product_db_batch_size){
                $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, update_date) VALUES ";
                $query .= implode( ', ', $place_holders );
                $wpdb->query($wpdb->prepare( $query, $values ));
                $batch_count = 0;
                $values = array();
                $place_holders = array();
              }
            } //end product list loop
          }// end product loop if
        }//end clategory loop
        /*
         * add last batch data in DB
         */
        if($batch_count > 0){
          $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, update_date) VALUES ";
          $query .= implode( ', ', $place_holders );
          $wpdb->query($wpdb->prepare( $query, $values ));          
        }

      }//end category if
      $total_product = $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data");
      $sync_produt = $total_product;
      $sync_produt_p = ($sync_produt*100)/$total_product; 
      $is_synced_up = ($total_product <= $sync_produt)?true:false;
      $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","conversios");
      //step one end
    }else if($sync_step == 2){      
      $rs = $TVCProductSyncHelper->call_batch_wise_sync_product($last_sync_product_id, $product_batch_size);
      if(isset($rs['products_sync'])){
        $sync_produt = (int)$sync_produt + $rs['products_sync'];
      }else{
        echo json_encode(array('status'=>'false', 'message'=> $rs['message'], "api_rs"=>$rs));
        exit;
      }
      $skip_products=(isset($rs['skip_products']))?$rs['skip_products']:0;
      $last_sync_product_id = (isset($rs['last_sync_product_id']))?$rs['last_sync_product_id']:0;
      $sync_produt_p = ($sync_produt*100)/$total_product;
      $is_synced_up = ($total_product <= $sync_produt)?true:false;
      $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","conversios");
      if($total_product <= $sync_produt){
        //$customObj->setGmcCategoryMapping($catMapRequest);
        //$customObj->setGmcAttributeMapping($attrMapRequest);
        $TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
        $TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db();
        $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","conversios");
        $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
      }
    }
    $sync_produt_p = round($sync_produt_p,0);
    $sync_progressive_data = array("sync_step"=>$sync_step, "total_product"=>$total_product, "sync_produt"=>$sync_produt, "sync_produt_p"=>$sync_produt_p, 'skip_products'=>$skip_products, "last_sync_product_id"=>$last_sync_product_id, "is_synced_up"=>$is_synced_up, "sync_message"=>$sync_message);
    echo json_encode(array('status'=>'success', "sync_progressive_data" => $sync_progressive_data, "api_rs"=>$rs));
    exit;
  }
  public function tvc_call_add_customer_feedback(){
    if( isset($_POST['que_one']) &&  isset($_POST['que_two']) && isset($_POST['que_three']) ){
      $formdata = array(); 
      $formdata['business_insights_index'] = sanitize_text_field($_POST['que_one']);
      $formdata['automate_integrations_index'] = sanitize_text_field($_POST['que_two']);
      $formdata['business_scalability_index'] = sanitize_text_field($_POST['que_three']);
      $formdata['subscription_id'] = isset($_POST['subscription_id'])?sanitize_text_field($_POST['subscription_id']):"";
      $formdata['customer_id'] = isset($_POST['customer_id'])?sanitize_text_field($_POST['customer_id']):"";
      $formdata['feedback'] = isset($_POST['feedback_description'])?sanitize_text_field($_POST['feedback_description']):"";
      $customObj = new CustomApi();
      unset($_POST['action']);    
      echo json_encode($customObj->record_customer_feedback($formdata));
      exit;
    }else{
      echo json_encode(array("error"=>true, "message" => esc_html__("Please answer the required questions","conversios") ));
    }   
  }
  public function tvc_call_add_survey(){
    if ( is_admin() ) {
      if(!class_exists('CustomApi')){
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }
      $customObj = new CustomApi();
      unset($_POST['action']);    
      echo json_encode($customObj->add_survey_of_deactivate_plugin($_POST));
      exit;
    }
  }
  //active licence key
  public function tvc_call_active_licence(){
    if ( is_admin() ) {
      $licence_key = isset($_POST['licence_key'])?sanitize_text_field($_POST['licence_key']):"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();      
      if($subscription_id!="" && $licence_key != ""){
        $response = $TVC_Admin_Helper->active_licence($licence_key, $subscription_id);
        
        if($response->error== false){
          //$key, $html, $title = null, $link = null, $link_title = null, $overwrite= false
          //$TVC_Admin_Helper->add_ee_msg_nofification("active_licence_key", esc_html__("Your plan is now successfully activated.","conversios"), esc_html__("Congratulations!!","conversios"), "", "", true);
          $TVC_Admin_Helper->update_subscription_details_api_to_db();
          echo json_encode(array('error' => false, "is_connect"=>true, 'message' => esc_html__("The licence key has been activated.","conversios") ));
        }else{
          echo json_encode(array('error' => true, "is_connect"=>true, 'message' => $response->message));
        }       
      }else if($licence_key != ""){ 
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['temp_active_licence_key'] = $licence_key;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);       
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => ""));
      }else{
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => esc_html__("Licence key is required.","conversios")));
      }      
    }
    exit;
  }

  public function tvc_call_notification_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'tvc_call_notification_dismiss-nonce')){      
      $ee_dismiss_id = isset($_POST['data']['ee_dismiss_id'])?sanitize_text_field($_POST['data']['ee_dismiss_id']):"";
      if($ee_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_msg_list = $TVC_Admin_Helper->get_ee_msg_nofification_list();
        if( isset($ee_msg_list[$ee_dismiss_id]) ){          
          unset($ee_msg_list[$ee_dismiss_id]);
          $ee_msg_list[$ee_dismiss_id]["active"]=0;
          $TVC_Admin_Helper->set_ee_msg_nofification_list($ee_msg_list);
          echo json_encode(array('status' => 'success', 'message' => ""));
        }        
      }       
    }
    exit;
  }
  public function tvc_call_notice_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce'), 'tvc_call_notice_dismiss-nonce')){      
      $ee_notice_dismiss_id = isset($_POST['data']['ee_notice_dismiss_id'])?sanitize_text_field($_POST['data']['ee_notice_dismiss_id']):"";
      $ee_notice_dismiss_id = sanitize_text_field($ee_notice_dismiss_id);
      if($ee_notice_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['dismissed_'.$ee_notice_dismiss_id] = 1;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        echo json_encode(array('status' => 'success', 'message' => $ee_additional_data));
      }       
    }
    exit;
  }
  public function tvc_call_import_gmc_product(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce'), 'tvc_call_api_sync-nonce')){
      $next_page_token = isset($_POST['next_page_token'])?sanitize_text_field($_POST['next_page_token']):"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $api_rs = $TVC_Admin_Helper->update_gmc_product_to_db($next_page_token);
      //print_r($api_rs);
      if( isset($api_rs['error']) ){
        echo json_encode($api_rs);
      }else{
        echo json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.","conversios")));
      }
      exit;
    }
    exit;
  }
  public function tvc_call_api_sync(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce'), 'tvc_call_api_sync-nonce')){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $api_rs = $TVC_Admin_Helper->set_update_api_to_db();
        if(isset($api_rs['error']) && isset($api_rs['message']) && sanitize_text_field($api_rs['message'])){
          echo json_encode($api_rs);
        }else{
          echo json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.","conversios")));
        }
        exit;
    }
    exit;
  }
  public function tvc_call_site_verified(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'SiteVerifiedNonce'), 'tvc_call_site_verified-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs =[];
      $tvc_rs = $TVC_Admin_Helper->call_site_verified();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }else{
        echo json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }      
      exit;
    }
    exit;
  }
  public function tvc_call_domain_claim(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiDomainClaimNonce'), 'tvc_call_domain_claim-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs = $TVC_Admin_Helper->call_domain_claim();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }else{
        echo json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }      
      exit;
    }
    exit;
  }
  public function get_tvc_access_token(){
    if(!empty($this->access_token)){
        return $this->access_token;
    }else{
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
        $this->access_token = sanitize_text_field(base64_decode($google_detail['setting']->access_token));
        return $this->access_token;
    }
  }
  
  public function get_tvc_refresh_token(){
    if(!empty($this->refresh_token)){
        return $this->refresh_token;
    }else{
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
        $this->refresh_token = sanitize_text_field(base64_decode($google_detail['setting']->refresh_token));
        return $this->refresh_token;
    }
  }
  /**
   * Delete the campaign
   */
  public function tvcajax_delete_campaign(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignDeleteNonce'), 'tvcajax-delete-campaign-nonce')){

          $merchantId = filter_input(INPUT_POST, 'merchantId');
          $customerId = filter_input(INPUT_POST, 'customerId');
          $campaignId = filter_input(INPUT_POST, 'campaignId');

          $url = $this->apiDomain.'/campaigns/delete';
          $data = [
              'merchant_id' => sanitize_text_field($merchantId),
              'customer_id' => sanitize_text_field($customerId),
              'campaign_id' => sanitize_text_field($campaignId)
          ];
          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'method' => 'DELETE',
              'body' => wp_json_encode($data)
          );
          // Send remote request
          $request = wp_remote_request(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              $message = $response_body->message;
              echo json_encode(['status' => 'success', 'message' => $message]);
          }else{
              $message = is_array($response_body->errors) ? $response_body->errors[0] : "Face some unprocessable entity";
              echo json_encode(['status' => 'error', 'message' => $message]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }
      }
      exit;
  }

  /**
   * Update the campaign status pause/active
   */
  public function tvcajax_update_campaign_status(){
    // make sure this call is legal
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignStatusNonce'), 'tvcajax-update-campaign-status-nonce')){
        if(!class_exists('ShoppingApi')){
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
        }

        $header = array(
            "Authorization: Bearer MTIzNA==",
            "Content-Type" => "application/json"
        );

        $merchantId = filter_input(INPUT_POST, 'merchantId');
        $customerId = filter_input(INPUT_POST, 'customerId');
        $campaignId = filter_input(INPUT_POST, 'campaignId');
        $budgetId = filter_input(INPUT_POST, 'budgetId');
        $campaignName = filter_input(INPUT_POST, 'campaignName');
        $budget = filter_input(INPUT_POST, 'budget');
        $status = filter_input(INPUT_POST, 'status');
        $curl_url = $this->apiDomain.'/campaigns/update';
        $shoppingObj = new ShoppingApi();
        $campaignData = $shoppingObj->getCampaignDetails($campaignId);

        $data = [
            'merchant_id' => sanitize_text_field($merchantId),
            'customer_id' => sanitize_text_field($customerId),
            'campaign_id' => sanitize_text_field($campaignId),
            'account_budget_id' => sanitize_text_field($budgetId),
            'campaign_name' => sanitize_text_field($campaignName),
            'budget' => sanitize_text_field($budget),
            'status' => sanitize_text_field($status),
            'target_country' => sanitize_text_field($campaignData->data['data']->targetCountry),
            'ad_group_id' => sanitize_text_field($campaignData->data['data']->adGroupId),
            'ad_group_resource_name' => sanitize_text_field($campaignData->data['data']->adGroupResourceName)
        ];
        
        $args = array(
          'headers' =>$header,
          'method' => 'PATCH',
          'body' => wp_json_encode($data)
        );
        $request = wp_remote_request(esc_url_raw($curl_url), $args);
        //print_r($request);
        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response = json_decode(wp_remote_retrieve_body($request));
        if (isset($response->error) && $response->error == false) {
          $message = $response->message;
          echo json_encode(['status' => 'success', 'message' => $message]);
        }else{
          $message = is_array($response->errors) ? $response->errors[0] : esc_html__("Face some unprocessable entity","conversios");
          echo json_encode(['status' => 'error', 'message' => $message]);
        }
    }
    exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_campaign_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignCategoryListsNonce'), 'tvcajax-campaign-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $url = $this->apiDomain.'/products/categories';

          $data = [
              'customer_id' => sanitize_text_field($customer_id),
              'country_code' =>sanitize_text_field( $country_code)
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_gmc_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'gmcCategoryListsNonce'), 'tvcajax-gmc-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $parent = filter_input(INPUT_POST, 'parent');
          $url = $this->apiDomain.'/products/gmc-categories';

          $data = [
              'customer_id' => sanitize_text_field($customer_id),
              'country_code' => sanitize_text_field($country_code),
              'parent' => sanitize_text_field($parent)
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

}
// End of TVC_Ajax_File_Class
endif;
$tvcajax_file_class = new TVC_Ajax_File();