<?php
/**
 * @since      4.0.2
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if ( ! class_exists( 'Conversios_Onboarding' ) ) {
	class Conversios_Onboarding {		
		protected $TVC_Admin_Helper;
		protected $subscriptionId;
		protected $version;
		protected $connect_url;
		protected $customApiObj;
		protected $app_id =1;
		protected $plan_id = 1;
		protected $tvc_data = array();
		protected $last_login;
		protected $is_refresh_token_expire;
		public function __construct( ){
			if ( ! is_admin() ) {
				return;
			}
			$this->includes();

			/**
			 *  Set Var
			 */
			$this->version = PLUGIN_TVC_VERSION;
			$this->customApiObj = new CustomApi();
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();

			$this->connect_url =  $this->TVC_Admin_Helper->get_connect_url();
			$this->tvc_data = $this->TVC_Admin_Helper->get_store_data();
			$this->is_refresh_token_expire = $this->TVC_Admin_Helper->is_refresh_token_expire();
			/**
				* check last login for check RefreshToken
				*/
			if(isset($ee_additional_data['ee_last_login']) && $ee_additional_data['ee_last_login'] != ""){
				$this->last_login = $ee_additional_data['ee_last_login'];
				$current = current_time( 'timestamp' );
				$diffrent_days = floor(( $current - $this->last_login)/(60*60*24));
				if($diffrent_days < 100){
					$this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();
					$g_mail = get_option('ee_customer_gmail');
					$this->tvc_data['g_mail']="";
					if($g_mail){
						$this->tvc_data['g_mail']= sanitize_email($g_mail);
					}
				}
			}

			/**
			 *  call Hooks and function
			 */
			add_action( 'admin_menu', array( $this, 'register' ) );
			// Add the welcome screen to the network dashboard.
			add_action( 'network_admin_menu', array( $this, 'register' ) );
			if($this->subscriptionId == ""){
				add_action( 'admin_init', array( $this, 'maybe_redirect' ), 9999 );
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );			
		}
		public function includes() {
	    if (!class_exists('CustomApi.php')) {
	      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
	    }   
	  }	

	  public function get_countries($user_country) {
        $getCountris = file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
        $contData = json_decode($getCountris);
        if (!empty($user_country)) {
            $data = "<select id='selectCountry' name='country' class='form-control slect2bx' readonly='true'>";
            $data .= "<option value=''>".esc_html__("Please select country","conversios")."</option>";
            foreach ($contData as $key => $value) {
                $selected = ($value->code == $user_country) ? "selected='selected'" : "";
                $data .= "<option value=" . esc_attr($value->code) . " " . esc_attr($selected) . " >" . esc_attr($value->name) . "</option>";
            }
            $data .= "</select>";
        } else {
            $data = "<select id='selectCountry' name='country' class='form-control slect2bx'>";
            $data .= "<option value=''>".esc_html__("Please select country","conversios")."</option>";
            foreach ($contData as $key => $value) {
              $data .= "<option value=" . esc_attr($value->code) . ">" . esc_attr($value->name) . "</option>";
            }
            $data .= "</select>";
        }
        return $data;
    }
	  public function is_checked($tracking_option, $is_val){        
      if($tracking_option == $is_val){
        return 'checked="checked"';
      }
    }
		/**
		 * onboarding page HTML
		 */
		public function welcome_screen() {
			$googleDetail = "";
			$defaulSelection = 1;
			$tracking_option = "UA";
			$login_customer_id ="";
			$completed_last_step ="step-0";
			$complete_step = array("step-0"=>1,"step-1"=>0,"step-2"=>0,"step-3"=>0);
			
			if ( isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id'])){
				$this->subscriptionId = sanitize_text_field($_GET['subscription_id']);
				if ( isset($_GET['g_mail']) && sanitize_email($_GET['g_mail'])){
					$this->tvc_data['g_mail'] = sanitize_email($_GET['g_mail']);
					$completed_last_step ="step-1";
					$complete_step["step-0"] = 1;

					$ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
					$ee_additional_data['ee_last_login'] = sanitize_text_field(current_time( 'timestamp' ));
					$this->TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);

					$this->is_refresh_token_expire = false;
				}
			}

			if($this->subscriptionId != ""){
				$google_detail = $this->customApiObj->getGoogleAnalyticDetail($this->subscriptionId);		
	  		if(property_exists($google_detail,"error") && $google_detail->error == false){
	  			if( property_exists($google_detail, "data") && $google_detail->data != "" ){
		        $googleDetail = $google_detail->data;
		        $this->tvc_data['subscription_id'] = $googleDetail->id;
		        $this->tvc_data['access_token'] = base64_encode(sanitize_text_field($googleDetail->access_token));
		        $this->tvc_data['refresh_token'] = base64_encode(sanitize_text_field($googleDetail->refresh_token));
		        $this->plan_id = $googleDetail->plan_id;
		        $login_customer_id = $googleDetail->customer_id;
		        $tracking_option = $googleDetail->tracking_option;
		        if($googleDetail->tracking_option != ''){
		          $defaulSelection = 0;
		        }
		        if($this->tvc_data['g_mail'] != ""){
			        if($googleDetail->measurement_id != "" || $googleDetail->property_id != ""){
			        	$complete_step["step-1"] = 1;
			        }
			        if($googleDetail->google_ads_id != "" ){
			        	$complete_step["step-2"] = 1;
			        }
			        if($googleDetail->google_merchant_center_id != "" ){
			        	$complete_step["step-3"] = 1;
			        }
			      }
		      }
	  		}
			}

			$is_e_e_tracking = (property_exists($googleDetail,"enhanced_e_commerce_tracking") && $googleDetail->enhanced_e_commerce_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");
      $is_u_t_tracking = (property_exists($googleDetail,"user_time_tracking") && $googleDetail->user_time_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");
      $is_a_g_snippet = (property_exists($googleDetail,"add_gtag_snippet") && $googleDetail->add_gtag_snippet == 1)?"checked":(($defaulSelection == 1)?"checked":"");
      $is_c_i_tracking = (property_exists($googleDetail,"client_id_tracking") && $googleDetail->client_id_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");
      $is_e_tracking = (property_exists($googleDetail,"exception_tracking") && $googleDetail->exception_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");
      $is_e_l_a_tracking = (property_exists($googleDetail,"enhanced_link_attribution_tracking") && $googleDetail->enhanced_link_attribution_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");

      $countries = json_decode(file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json"));
      $credit = json_decode(file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/country_reward.json"));

      $off_country = "";
      $off_credit_amt = "";
      if(is_array($countries) || is_object($countries)){
        foreach( $countries as $key => $value ){
          if($value->code == $this->tvc_data['user_country']){
            $off_country = $value->name;
            break;
          }
        }
      }

      if(is_array($credit) || is_object($credit)){
        foreach( $credit as $key => $value ){
          if($value->name == $off_country){
            $off_credit_amt = $value->price;
            break;
          }
        }
      }
		?>
		<style>
			#menu-dashboard li.current{display: none;}
			#wpadminbar{display: none;}
		</style>
		<div class="bodyrightpart onbordingbody-wapper">
			<div class="loader-section" id="loader-section"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/ajax-loader.gif');?>" alt="loader"></div>
			<div class="alert-message" id="tvc_onboarding_popup_box"></div>
			<div class="onbordingbody">
				<div class="site-header">
				  <div class="container">
				    <div class="brand"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/logo.png');?>" alt="Conversios" /></div>
				  </div>
				</div>
				<div class="onbording-wrapper">
			    <div class="container">
			      <div class="smallcontainer">
			        <div class="onbordingtop">
		            <h2><?php esc_html_e("Let’s get you started.","conversios"); ?></h2>
		            <p><?php esc_html_e("Automate Google Analytics, Dynamic Remarketing & Google Shopping in just 5 minutes.","conversios"); ?></p>
		          </div>
		          <div class="row">
		            <!-- onborading left start -->
								<div class="onboardingstepwrap">									
									<!-- step-0 start -->
								  <div class="onbordording-step onbrdstep-0 gglanystep <?php if($this->subscriptionId == "" || $this->tvc_data['g_mail']=="" || $this->is_refresh_token_expire == true ){ echo "activestep"; }else{echo "selectedactivestep"; } ?>">
							      <div class="stepdtltop" data-is-done="<?php echo esc_attr($complete_step['step-0']); ?>" id="google-signing" data-id="step_0">
						          <div class="stepleftround">
						            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check-wbg.png'); ?>" alt="" />
						          </div>
						          <div class="stepdetwrap">
					              <h4><?php esc_html_e("Connect Conversios with your website","conversios"); ?></h4>
					              <p><?php echo (isset($this->tvc_data['g_mail']) && esc_attr($this->subscriptionId) )?esc_attr($this->tvc_data['g_mail']):""; ?></p>
						          </div>
							      </div>
							      <div class="stepmoredtlwrp">
						          <div class="stepmoredtl">						          	
						          	<?php if(!isset($this->tvc_data['g_mail']) || $this->tvc_data['g_mail'] == "" || $this->subscriptionId == ""){?>
						          		<div class="google_connect_url google-btn">
													  <div class="google-icon-wrapper">
													    <img class="google-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/g-logo.png'); ?>"/>
													  </div>
													  <p class="btn-text"><b><?php esc_html_e("Sign in with google","conversios"); ?></b></p>
													</div>
						          	<?php } else{?>
						          		
													<?php if($this->is_refresh_token_expire == true){?>
														<p class="alert alert-primary"><?php esc_html_e("It seems the token to access your Google accounts is expired. Sign in again to continue.","conversios"); ?></p>
														<div class="google_connect_url google-btn">
														  <div class="google-icon-wrapper">
														    <img class="google-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/g-logo.png'); ?>"/>
														  </div>
														  <p class="btn-text"><b><?php esc_html_e("Sign in with google","conversios"); ?></b></p>
														</div>
													<?php } else{ ?>
														<div class="google_connect_url google-btn">
														  <div class="google-icon-wrapper">
														    <img class="google-icon" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/g-logo.png'); ?>"/>
														  </div>
														  <p class="btn-text mr-35"><b><?php esc_html_e("Reauthorize","conversios"); ?></b></p>
														</div>
													<?php } ?>
												<?php } ?>
						          	<p><?php esc_html_e("Make sure you sign in with the google account that has all privileges to access google analytics, google ads and google merchant center account.","conversios"); ?></p>						          	
						          </div>
						        </div>
								  </div>
								  <!-- step-0 over -->
								  <!-- step-1 start -->
								  <div class="onbordording-step onbrdstep-1 gglanystep <?php echo ($complete_step['step-1']==1 && $this->tvc_data['g_mail'] && $this->is_refresh_token_expire == false )?'selectedactivestep':''; ?> <?php if($this->subscriptionId != "" && $this->tvc_data['g_mail'] && $this->is_refresh_token_expire == false){ echo "activestep"; } ?>">
							      <div class="stepdtltop" data-is-done="<?php echo esc_attr($complete_step['step-1']); ?>" id="google-analytics" data-id="step_1">
						          <div class="stepleftround">
						            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check-wbg.png'); ?>" alt="" />
						          </div>
						          <div class="stepdetwrap">
					              <h4><?php esc_html_e("Connect Google Analytics Account","conversios"); ?></h4>
					              <p><?php esc_html_e("Tag your website with all important e-commerce events in Google Analytics.","conversios"); ?></p>
						          </div>
							      </div>
							      <div class="stepmoredtlwrp">
						          <div class="stepmoredtl">
						            <form action="#">
					                <div class="form-row">
					                  <h5><?php esc_html_e("How do you plan to tag your website?","conversios"); ?></h5>
				                    <div class="cstmrdobtn-item">
				                      <label for="univeral">
			                          <input type="radio" <?php echo esc_attr($this->is_checked($tracking_option, "UA")); ?> name="analytic_tag_type" id="univeral" value="UA">
			                          <span class="checkmark"></span>
			                          <?php esc_html_e("Universal Analytics (Google Analytics 3)","conversios"); ?>
				                      </label>
				                      <div id="UA" class="slctunivr-filed">
			                          <div class="tvc-dropdown"> 
																  <div class="tvc-dropdown-header" id="ua_web_property_id_option_val" data-profileid=""  data-accountid="<?php if($googleDetail->ua_analytic_account_id){ echo esc_attr($googleDetail->ua_analytic_account_id); } ?>" data-val="<?php if($googleDetail->property_id){ echo esc_attr($googleDetail->property_id); } ?>"><?php if($googleDetail->property_id){
																  	echo esc_attr($googleDetail->property_id);
																  }else{?><?php esc_html_e("Select Property Id","conversios"); ?><?php } ?></div>
																  <div class="tvc-dropdown-content" id="ua_web_property_id_option">
																    <div class="tvc-select-items"><option value=""><?php esc_html_e("Select Property Id","conversios"); ?></option></div>      
																    <div class="tvc-ua-option-more option"><?php esc_html_e("Load More","conversios"); ?></div>
																  </div>
																</div>

				                      </div>
				                    </div>
					                    <div class="cstmrdobtn-item">
					                      <label for="gglanytc">
				                          <input type="radio" <?php echo esc_attr($this->is_checked($tracking_option, "GA4")); ?> name="analytic_tag_type" id="gglanytc" value="GA4">
				                          <span class="checkmark"></span>
				                          <?php esc_html_e("Google Analytics 4","conversios"); ?>
					                      </label>
					                      <div id="GA4" class="slctunivr-filed">
				                          
				                          <div class="tvc-dropdown"> 
																	  <div class="tvc-dropdown-header" id="ga4_web_measurement_id_option_val" data-name="" data-accountid="<?php if($googleDetail->ga4_analytic_account_id){ echo esc_attr($googleDetail->ga4_analytic_account_id); } ?>" data-val="<?php if($googleDetail->measurement_id){ echo esc_attr($googleDetail->measurement_id); } ?>">
																	  <?php if($googleDetail->measurement_id){
																  		echo esc_attr($googleDetail->measurement_id);
																  	}else{?><?php esc_html_e("Select Measurement Id","conversios"); ?>
																  	<?php } ?></div>
																	  <div class="tvc-dropdown-content" id="ga4_web_measurement_id_option">
																	    <div class="tvc-select-items"><option value=""><?php esc_html_e("Select Measurement Id","conversios"); ?></option></div>      
																	    <div class="tvc-ga4-option-more option"><?php esc_html_e("Load More","conversios"); ?></div>
																	  </div>
																	</div>

					                      </div>
					                    </div>
					                    <div class="cstmrdobtn-item">
					                      <label for="both">
				                          <input type="radio" <?php echo esc_attr($this->is_checked($tracking_option, "BOTH")); ?> name="analytic_tag_type" id="both" value="BOTH">
				                          <span class="checkmark"></span>
				                          <?php esc_html_e("Both","conversios"); ?>
					                      </label>
					                      <div id="BOTH" class="slctunivr-filed">
				                          <div class="botslectbxitem">
				                            
				                          	<div class="tvc-dropdown"> 
																		  <div class="tvc-dropdown-header" id="both_ua_web_property_id_option_val" data-profileid="" data-accountid="<?php if($googleDetail->ua_analytic_account_id){ echo esc_attr($googleDetail->ua_analytic_account_id); } ?>" data-val="<?php if($googleDetail->property_id){ echo esc_attr($googleDetail->property_id); } ?>"><?php if($googleDetail->property_id){
																  	echo esc_attr($googleDetail->property_id);
																  }else{?><?php esc_html_e("Select Property Id","conversios"); ?><?php } ?></div>
																		  <div class="tvc-dropdown-content" id="both_ua_web_property_id_option">
																		    <div class="tvc-select-items"><option value=""><?php esc_html_e("Select Property Id","conversios"); ?></option></div>      
																		    <div class="tvc-ua-option-more option"><?php esc_html_e("Load More","conversios"); ?></div>
																		  </div>
																		</div>

				                          </div>
				                          <div class="botslectbxitem">
			                              
				                            <div class="tvc-dropdown"> 
																		  <div class="tvc-dropdown-header" id="both_ga4_web_measurement_id_option_val" data-name="" data-accountid="<?php if($googleDetail->ga4_analytic_account_id){ echo esc_attr($googleDetail->ga4_analytic_account_id); } ?>" data-val="<?php if($googleDetail->measurement_id){ echo esc_attr($googleDetail->measurement_id); } ?>">
																		  	<?php if($googleDetail->measurement_id){
																  		echo esc_attr($googleDetail->measurement_id);
																  	}else{?><?php esc_html_e("Select Measurement Id","conversios"); ?>
																  	<?php } ?></div>
																		  <div class="tvc-dropdown-content" id="both_ga4_web_measurement_id_option">
																		    <div class="tvc-select-items"><option value=""><?php esc_html_e("Select Measurement Id","conversios"); ?></option></div>      
																		    <div class="tvc-ga4-option-more option"><?php esc_html_e("Load More","conversios"); ?></div>
																		  </div>
																		</div>
				                          </div>
				                          <div id="old_tracking" data-tracking_option="<?php echo esc_attr($tracking_option); ?>" data-measurement_id="<?php echo esc_attr($googleDetail->measurement_id); ?>" data-property_id="<?php echo esc_attr($googleDetail->property_id); ?>"></div>
					                      </div>
					                    </div>
					                </div>
					                <div class="form-row">
				                    <h5><?php esc_html_e("Advance Settings (Optional)","conversios"); ?></h5>
				                    <div class="chckbxbgbx">
			                        <div class="cstmcheck-item">
		                            <label for="enhanced_e_commerce_tracking">
		                              <input type="checkbox"  class="custom-control-input" name="enhanced_e_commerce_tracking" id="enhanced_e_commerce_tracking" <?php echo esc_attr($is_e_e_tracking); ?>>
		                              <span class="checkmark"></span>
		                                <?php esc_html_e("Enhaced e-commerce tracking","conversios"); ?>
		                            </label>
			                        </div>
			                        <div class="cstmcheck-item">
		                            <label for="add_gtag_snippet">
		                              <input type="checkbox" class="custom-control-input" name="add_gtag_snippe" id="add_gtag_snippet" <?php echo esc_attr($is_a_g_snippet); ?>>
		                              <span class="checkmark"></span>
		                                <?php esc_html_e("Add gtag.js snippet","conversios"); ?>
		                            </label>
			                        </div>
				                    </div>
					                </div>
					                <div class="stepsbmtbtn">
					                	<input type="hidden" id="subscriptionPropertyId" name="subscriptionPropertyId"  value="<?php echo (property_exists($googleDetail,"property_id"))?esc_attr($googleDetail->property_id):""; ?>">
					                	<input type="hidden" id="subscriptionMeasurementId" name="subscriptionMeasurementId" value="<?php echo (property_exists($googleDetail,"measurement_id"))?esc_attr($googleDetail->measurement_id):""; ?>">
					                  <button type="button" disabled id="step_1" class="stepnextbtn stpnxttrgr"><?php esc_html_e("Next","conversios"); ?></button>
					                </div>
						            </form>
						          </div>
							      </div>
								  </div>
								  <!-- step-1 over -->
								  <!-- step-2 start -->
								  <div class="onbordording-step onbrdstep-2 ggladsstep <?php echo ($complete_step['step-2']==1 && $this->is_refresh_token_expire == false)?'selectedactivestep':''; ?>">
							      <div class="stepdtltop" data-is-done="<?php echo esc_attr($complete_step['step-2']); ?>" id="google-ads" data-id="step_2">
						          <div class="stepleftround">
						            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check-wbg.png'); ?>" alt="" />
						          </div>
						          <div class="stepdetwrap">
						            <h4><?php esc_html_e("Select Google Ads account","conversios"); ?></h4>
						            <p><?php esc_html_e("With dynamic reamarketing tags, you will be able to show ads to your past visitors with specific product information tailored to your customer’s previous site visit.","conversios"); ?></p>
						          </div>
							      </div>
							      <div class="stepmoredtlwrp">
						          <div class="stepmoredtl">
						            <form action="#">
						              <div class="selcttopwrap" id="tvc_ads_section">
						                <div class="ggladsselectbx">
						                	<input type="hidden" id="subscriptionGoogleAdsId" name="subscriptionGoogleAdsId" value="<?php echo property_exists($googleDetail,"google_ads_id")?esc_attr($googleDetail->google_ads_id):""; ?>">
						                  <select class="slect2bx google_ads_sel" id="ads-account" name="customer_id">
					                      <option value=''><?php esc_html_e("Select Google Ads Account","conversios"); ?></option>  
						                  </select>
						                </div>
						                <div class="orwrp"><?php esc_html_e("or","conversios"); ?></div>
						                <div class="creatnewwrp">
						                  <button type="button" class="cretnewbtn newggladsbtn"><span class="plusicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png'); ?>" alt="" /></span> <?php esc_html_e("Create New","conversios"); ?></button>
						                </div>
						              </div>

						              <div class="selcttopwrap">                          
	                          <div class="onbrdpp-body alert alert-primary" style="display:none;" id="new_google_ads_section">
                              <h4><?php esc_html_e("Account Created","conversios"); ?></h4>
                              <p><?php esc_html_e("Your Google Ads Account has been created","conversios"); ?> <strong>(<b><span id="new_google_ads_id"></span></b>).</strong></p>
                             	<h5><?php esc_html_e("Steps to claim your Google Ads Account:","conversios"); ?></h5>
                              <ol>
						                    <li><?php esc_html_e("Accept invitation mail from Google Ads sent to your email address","conversios"); ?> <em><?php echo (isset($this->tvc_data['g_mail']))?esc_attr($this->tvc_data['g_mail']):""; ?></em><span id="invitationLink"><br><em>OR</em> Open
						                    	<a href="" target="_blank" id="ads_invitationLink">Invitation Link</a></span>
						                    </li>
						                    <li><?php esc_html_e("Log into your Google Ads account and set up your billing preferences","conversios"); ?></li>
							                </ol>                          
	                          </div>
	                        </div>

						              <div class="form-row">
						              	<?php
						              	$is_r_tags = (property_exists($googleDetail,"remarketing_tags") && $googleDetail->remarketing_tags == 1)?"checked":(($defaulSelection == 1)?"checked":"");
		                        $is_l_g_an_w_g_ad = (property_exists($googleDetail,"link_google_analytics_with_google_ads") && $googleDetail->link_google_analytics_with_google_ads == 1)?"checked":(($defaulSelection == 1)?"checked":"");
		                        $is_d_r_tags = (property_exists($googleDetail,"dynamic_remarketing_tags") && $googleDetail->dynamic_remarketing_tags == 1)?"checked":(($defaulSelection == 1)?"checked":"");
		                        $is_g_ad_c_tracking = (property_exists($googleDetail,"google_ads_conversion_tracking") && $googleDetail->google_ads_conversion_tracking == 1)?"checked":(($defaulSelection == 1)?"checked":"");
		                        ?>
						                <h5><?php esc_html_e("Advance Settings (Optional)","conversios"); ?></h5>
					                  <div class="chckbxbgbx dsplcolmview">
				                      <div class="cstmcheck-item">
			                          <label for="remarketing_tag">
			                            <input type="checkbox" class="custom-control-input" name="remarketing_tag" id="remarketing_tag" value="1" <?php echo esc_attr($is_r_tags); ?>>
			                            <span class="checkmark"></span>
			                              <?php esc_html_e("Enable Google Remarketing Tag","conversios"); ?>
			                          </label>
				                      </div>
				                      <div class="cstmcheck-item">
			                          <label for="dynamic_remarketing_tags">
			                            <input type="checkbox" class="custom-control-input" name="dynamic_remarketing_tags" id="dynamic_remarketing_tags" value="1" <?php echo esc_attr($is_d_r_tags); ?>>
			                            <span class="checkmark"></span>
			                              <?php esc_html_e("Enable Dynamic Remarketing Tag","conversios"); ?>
			                          </label>
				                      </div>
				                       <div class="cstmcheck-item <?php if($this->plan_id == 1){?>cstmcheck-item-pro <?php } ?>">
			                          <label for="google_ads_conversion_tracking">
			                          	<?php if($this->plan_id != 1){?>
				                            <input type="checkbox" class="custom-control-input" name="google_ads_conversion_tracking" id="google_ads_conversion_tracking" value="1" <?php echo esc_attr($is_g_ad_c_tracking); ?>>
				                            <span class="checkmark"></span>
				                            <?php esc_html_e("Google Ads conversion tracking","conversios"); ?>
				                          <?php }else{?>
				                          	<img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/lock.svg'); ?>"><label><?php esc_html_e("Google Ads conversion tracking (Pro Plan)","conversios"); ?></label>
				                          <?php } ?>                 				                          
			                          </label>
				                      </div>
				                      <div class="cstmcheck-item">
			                          <label for="link_google_analytics_with_google_ads">
			                             <input type="checkbox" class="custom-control-input" name="link_google_analytics_with_google_ads" id="link_google_analytics_with_google_ads" value="1" <?php echo esc_attr($is_l_g_an_w_g_ad); ?>>
			                            <span class="checkmark"></span>
			                              <?php esc_html_e("Link Google Analytics with Google Ads","conversios"); ?>
			                          </label>
				                      </div>				                      
					                  </div>
						              </div>
						              <div class="stepsbmtbtn">
						                <button type="button" id="step_2" class="stepnextbtn stpnxttrgr"><?php esc_html_e("Next","conversios"); ?></button>
						                  <!-- add dslbbtn class for disable button -->
						              </div>
						            </form>
						          </div>
							      </div>
								  </div>
								  <!-- step-2 over -->
								  <!-- step-3 start -->
								  <div class="onbordording-step onbrdstep-3 gglmrchntstep <?php echo ($complete_step['step-3']==1 && $this->is_refresh_token_expire == false )?'selectedactivestep':''; ?>">
							      <div class="stepdtltop" data-is-done="<?php echo esc_attr($complete_step['step-3']); ?>" id="gmc-account" data-id="step_3">
						          <div class="stepleftround">
						            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check-wbg.png'); ?>" alt="" />
						          </div>
						          <div class="stepdetwrap">
					              <h4><?php esc_html_e("Select Google Merchant Center Account","conversios"); ?></h4>
					              <p><?php esc_html_e("Make your WooCommerce shop and products available to millions of shoppers across google.","conversios"); ?></p>
						          </div>
							      </div>
							      <div class="stepmoredtlwrp">
						          <div class="stepmoredtl">
						            <form action="#">
					                <div class="selcttopwrap">
					                	<div class="form-group" style="display:none;" id="new_merchant_section">
		                          <div class="text-center">                        
		                            <div class="alert alert-primary" style="padding: 10px;" role="alert">                          
		                              <label class="form-label-control font-weight-bold"><?php esc_html_e("We have created new merchant center account with ID: ","conversios"); ?><span id="new_merchant_id"></span>. <?php esc_html_e("Click on finish button to save new account.","conversios"); ?></label>
		                            </div>
		                          </div>
		                        </div>
		                        <div id="tvc_merchant_section">
						                  <div class="ggladsselectbx">
						                    <select class="slect2bx" id="google_merchant_center_id" name="google_merchant_center_id">
					                        <option value=''><?php esc_html_e("Select Google Merchant Center","conversios"); ?></option>   
						                    </select>
						                  </div>
						                  <div class="orwrp"><?php esc_html_e("or","conversios"); ?></div>
						                  <div class="creatnewwrp">
						                    <button type="button" class="cretnewbtn newmrchntbtn"><span class="plusicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/blue-plus.png'); ?>" alt="" /></span> <?php esc_html_e("Create New","conversios"); ?></button>
						                  </div>
						                </div>
					                </div>
						              <div class="stepsbmtbtn">
						                <button type="button" id="step_3" data-enchanter="finish" class="stepnextbtn finishbtn"><?php esc_html_e("Finish","conversios"); ?></button>
						                <!-- add dslbbtn class for disable button -->
						              </div>
						              <input type="hidden" id="subscriptionMerchantCenId" name="subscriptionMerchantCenId" value="<?php echo property_exists($googleDetail,"google_merchant_center_id")?esc_attr($googleDetail->google_merchant_center_id):""; ?>">
                          <input type="hidden" id="loginCustomerId" name="loginCustomerId"  value="<?php echo esc_attr($login_customer_id); ?>">
                          <input type="hidden" id="subscriptionId" name="subscriptionId"  value="<?php echo esc_attr($this->subscriptionId); ?>">
                          <input type="hidden" id="plan_id" name="plan_id" value="<?php echo esc_attr($this->plan_id); ?>">
						              <input type="hidden" id="conversios_onboarding_nonce" name="conversios_onboarding_nonce" value="<?php echo wp_create_nonce( 'conversios_onboarding_nonce' ); ?>">

						              <input type="hidden" id="ga_view_id" name="ga_view_id" value="">
						            </form>
						          </div>
						          <div class="stepnotewrp">
						            <?php esc_html_e('If you are in the European Economic Area or Switzerland your Merchant Center account must be associated with a Comparison Shopping Service (CSS). Please find more information at Google Merchant Center Help website. If you create a new Merchant Center account through this application, it will be associated with Google Shopping, Google’s CSS, by default. You can change the CSS associated with your account at any time. Please find more information about our CSS Partners <a href="">here</a>. Once you have set up your Merchant Center account you can use our onboarding tool regardless of which CSS you use.','conversios'); ?>
						          </div>
							      </div>
								  </div>
								  <!-- step-3 over -->
								</div> 
								<!-- onborading left over -->
	              <!-- onborading right panel start -->
	              <div class="onbording-right">
	                <div class="sidebrcontainer">
	                  <div class="onbrd-rdmbx">
	                    <div class="rdm-amnt">
	                      <small><?php esc_html_e("Google Ads Credit of","conversios"); ?></small>
	                      <?php echo esc_attr($off_credit_amt); ?>
	                    </div>
	                    <p><?php esc_html_e("New users can get ".esc_attr($off_credit_amt)." in Ad Credits when they spend their first ".esc_attr($off_credit_amt)." on Google Ads within 60 days.","conversios"); ?></p>
	                    <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/new-google-spend-match.pdf"); ?>" class="lrnmorbtn"><?php esc_html_e("Terms and conditions apply","conversios"); ?> <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/arrow_right.png'); ?>" alt="" /></a>
	                  </div>
	                  <div class="onbrdrgt-nav">
	                    <ul>
	                      <li><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/Installation-Manual.pdf"); ?>"><?php echo esc_html_e("Installation Manual","conversios"); ?></a></li>
	                      <li><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/Google-shopping-Guide.pdf"); ?>" href=""><?php esc_html_e("Google Shopping Guide","conversios"); ?></a></li>
	                      <li><a target="_blank" href="<?php echo esc_url_raw("https://wordpress.org/plugins/enhanced-e-commerce-for-woocommerce-store/faq/"); ?>" href=""><?php esc_html_e("FAQ","conversios"); ?></a></li>
	                    </ul>
	                  </div>
	                </div>
	              </div>
	              <!-- onborading right panel over -->
		          </div>
			      </div>
			    </div>
				</div>
			</div>
		</div>
		<!-- google ads skip confirm poppup -->
		<div class="pp-modal onbrd-popupwrp" id="tvc_ads_skip_confirm" tabindex="-1" role="dialog">
      <div class="onbrdppmain" role="document">
        <div class="onbrdnpp-cntner acccretppcntnr">
          <div class="onbrdnpp-hdr">
          	<h4><?php esc_html_e("You have not selected Google Ads account.","conversios"); ?></h4>
            <div class="ppclsbtn clsbtntrgr"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-icon.png');?>" alt="" /></div>
          </div>
          <div class="onbrdpp-body">
            <p><?php esc_html_e("If you do not select Google Ads account, you will not be able to use some of the major features like:","conversios"); ?></p>
            <ul>
              <li><?php esc_html_e("Dynamic Remarketing Tags","conversios"); ?> </li>
              <li><?php esc_html_e("Google Smart Shopping Campaigns","conversios"); ?></li>
              <li><?php esc_html_e("Google Analytics and Google Ads linking","conversios"); ?></li>
            </ul>
            <p><?php esc_html_e("Are you sure you want to continue without selecting Google Ads account?","conversios"); ?></p>
          </div>
          <div class="ppfooterbtn">
            <button type="button" class="ppblubtn btn-secondary" data-dismiss="modal" id="ads-skip-cancel"><?php esc_html_e("Cancel","conversios"); ?></button>
            <button type="button" class="ppblubtn btn-primary" data-dismiss="modal" id="ads-skip-continue"><?php esc_html_e("Continue","conversios"); ?></button>
          </div>
        </div>
      </div>
    </div>
		<!-- google ads poppup -->
		<div id="ggladspopup" class="pp-modal onbrd-popupwrp ggladspp">
	    <div class="onbrdppmain">
        <div class="onbrdnpp-cntner ggladsppcntnr">
          <div class="onbrdnpp-hdr">
            <h4><?php esc_html_e("Enable Google Ads Account","conversios"); ?></h4>
            <div class="ppclsbtn clsbtntrgr"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-icon.png');?>" alt="" /></div>
          </div>
          <div class="onbrdpp-body">
            <p><?php esc_html_e("You’ll receive an invite from Google on your email. Accept the invitation to enable your Google Ads Account.","conversios"); ?></p>
          </div>
          <div class="ppfooterbtn">
            <button type="button" id="ads-continue" class="ppblubtn sndinvitebtn"><?php esc_html_e("Send Invite","conversios"); ?></button>
          </div>
        </div>
	    </div>
		</div>
		<!-- merchant center skip confirm -->
		<div class="pp-modal onbrd-popupwrp" id="tvc_merchant_center_skip_confirm">
      <div class="onbrdppmain">
        <div class="onbrdnpp-cntner acccretppcntnr">
          <div class="onbrdnpp-hdr">
            <h4><?php esc_html_e("You have not selected Google merchant center account.","conversios"); ?></h4>
            <div class="ppclsbtn clsbtntrgr"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-icon.png');?>" alt="" /></div>
          </div>
          <div class="onbrdpp-body">
            <p><?php esc_html_e("If you do not select a merchant center account, you will not be able to use complete google shopping features.","conversios"); ?></p>
            <p><?php esc_html_e("Are you sure you want to continue without selecting a merchant center account?","conversios"); ?></p>
          </div>
          <div class="ppfooterbtn">
            <button type="button" class="ppblubtn btn-secondary" data-dismiss="modal" id="merchant-center-skip-cancel"><?php esc_html_e("Cancel","conversios"); ?></button>
            <button type="button" class="ppblubtn btn-primary" data-dismiss="modal" id="merchant-center-skip-continue"><?php esc_html_e("Continue","conversios"); ?></button>
          </div>
        </div>
      </div>
    </div>
		<!-- Create New Merchant poppup -->
		<div id="createmerchantpopup" class="pp-modal onbrd-popupwrp crtemrchntpp">
	    <div class="onbrdppmain">
        <div class="onbrdnpp-cntner crtemrchntppcntnr">
          <div class="ppclsbtn clsbtntrgr"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-icon.png'); ?>" alt="" /></div>
          <div class="onbrdpp-body">
            <div class="row">
              <div class="crtemrchnpp-lft">
                <div class="crtemrchpplft-top">
                  <h4><?php esc_html_e("Create Google Merchant Center Account","conversios"); ?></h4>
                  <p><?php esc_html_e("Before you can upload product data, you’ll need to verify and claim your store’s website URL. Claiming associates your website URL with your Google Merchant Center account.","conversios"); ?></p>
                </div>
                <div class="claimedbx">
                    <?php esc_html_e("Your site will automatically be claimed and verified.","conversios"); ?>
                </div>
                <div class="mrchntformwrp">
                  <form action="#">
                    <div class="form-row">
                    	<input type="hidden" id="get-mail" name="g_email" value="<?php echo isset($this->tvc_data['g_mail'])?esc_attr($this->tvc_data['g_mail']):""; ?>">
                    	<input type="text" value="<?php echo esc_attr($this->tvc_data['user_domain']); ?>" class="fromfiled" name="url" id="url" placeholder="Enter Website">
                      <div class="cstmcheck-item mt15">
                        <label for="adult_content">
                          <input class="" type="checkbox" name="adult_content" id="adult_content">
                          <span class="checkmark"></span>
                          <?php esc_html_e("My site contains","conversios"); ?>
                        </label>
                        <strong><?php esc_html_e("Adult Content","conversios"); ?></strong>
                      </div>
                    </div>
                    <div class="form-row">
                      <input type="text" class="fromfiled" name="store_name" id="store_name" placeholder="<?php esc_html_e("Enter Store Name","conversios"); ?>" required>
                      <div class="inputinfotxt"><?php esc_html_e("This name will appear in your Shopping Ads.","conversios"); ?></div>
                    </div>
                    <div class="form-row">
                    	<?php echo $this->get_countries($this->tvc_data['user_country']); ?>
                    </div>
                    <div class="form-row">
                      <div class="cstmcheck-item">
                        <label for="terms_conditions">
                          <input class="" type="checkbox" name="concent"  id="terms_conditions">
                          <span class="checkmark"></span>
                          <?php esc_html_e("I accept the","conversios"); ?>
                        </label>
                        <a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/160173?hl=en"); ?>"><?php esc_html_e("terms & conditions","conversios"); ?></a>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="ppfooterbtn">
                  <button type="button" id="create_merchant_account" class="cretemrchntbtn"><?php esc_html_e("Create Account","conversios"); ?>
                  </button>
                </div>
              </div>
              <div class="crtemrchnpp-right">
                <h6><?php esc_html_e("To use Google Shopping, your website must meet these requirements:","conversios"); ?></h6>
                <ul>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6149970?hl=en"); ?>"><?php esc_html_e("Google Shopping ads policies","conversios"); ?></a></li>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6150127"); ?>"><?php esc_html_e("Accurate Contact Information","conversios"); ?></a></li>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6150122"); ?>"><?php esc_html_e("Secure collection of process and personal data","conversios"); ?></a></li>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6150127"); ?>"><?php esc_html_e("Return Policy","conversios"); ?></a></li>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6150127"); ?>"><?php esc_html_e("Billing terms & conditions","conversios"); ?></a></li>
                  <li><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/merchants/answer/6150118"); ?>"><?php esc_html_e("Complete checkout process","conversios"); ?></a></li>
                </ul>
              </div>
            </div>
          </div>
            
        </div>
	    </div>
		</div>

		<!-- congratulation poppup -->
		<div id="tvc_confirm_submite" class="pp-modal onbrd-popupwrp congratepp">
	    <div class="onbrdppmain">
        <div class="onbrdnpp-cntner congratppcntnr">
          <div class="onbrdnpp-hdr txtcnter">
            <h2><?php esc_html_e("Congratulations!!","conversios"); ?></h2>
            <div class="ppclsbtn clsbtntrgr"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-icon.png'); ?>" alt="" /></div>
          </div>
          <div class="onbrdpp-body congratppbody">
            <p><?php esc_html_e("You have been successfully onboarded. Please check the account summary below and confirm.","conversios"); ?></p>
            <div class="congratppdtlwrp">
              <div class="cngrtppdtl-item" id="google_analytics_property_id_info">
                <div class="cngtrpplft">
                  <span class="cngrtchckicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/green-check.png'); ?>" alt="" /></span>
                    <?php esc_html_e("Google Analytics Property Id:","conversios"); ?>
                </div>
                <div class="cngtrpprgt" id="selected_google_analytics_property"></div>
              </div>
              <div class="cngrtppdtl-item" id="google_analytics_measurement_id_info">
                <div class="cngtrpplft">
                  <span class="cngrtchckicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/green-check.png'); ?>" alt="" /></span>
                    <?php esc_html_e("Google Analytics Measurement Id:","conversios"); ?>
                </div>
                <div class="cngtrpprgt" id="selected_google_analytics_measurement"></div>
              </div>
              <div class="cngrtppdtl-item" id="google_ads_info">
                <div class="cngtrpplft">
                  <span class="cngrtchckicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/green-check.png'); ?>" alt="" /></span>
                    <?php esc_html_e("Google Ads Account:","conversios"); ?>
                </div>
                <div class="cngtrpprgt" id="selected_google_ads_account"></div>
              </div>
              <div class="cngrtppdtl-item" id="google_merchant_center_info">
                <div class="cngtrpplft">
                  <span class="cngrtchckicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/green-check.png'); ?>" alt="" /></span>
                    <?php esc_html_e("Google Merchant Center Account","conversios"); ?>
                </div>
                <div class="cngtrpprgt" id="selected_google_merchant_center"></div>
              </div>
            </div>
          </div>
          <div class="ppfooterbtn">
            <button type="button" id="confirm_selection" class="ppblubtn"><?php esc_html_e("Confirm","conversios"); ?></button>
          </div>
        </div>
	    </div>
		</div>
		<?php
			$this->page_script();
		}
		/**
		 * onboarding page javascript
		 */
		public function page_script(){
			?>
			<script>

				var tvc_data = "<?php echo esc_js(wp_json_encode($this->tvc_data)); ?>";
				var tvc_ajax_url = '<?php echo esc_url_raw(admin_url( 'admin-ajax.php' )); ?>';
				let subscription_id ="<?php echo esc_attr($this->subscriptionId); ?>";
      	let plan_id ="<?php echo esc_attr($this->plan_id); ?>";
      	let app_id ="<?php echo esc_attr($this->app_id); ?>"; 
				/**
				 * Convesios custom script
				 */
				//Step-0
				jQuery(".google_connect_url").on( "click", function() {
		     	const w =600; const h=650;
				 	const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
			    const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

			    const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
			    const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

			    const systemZoom = width / window.screen.availWidth;
			    const left = (width - w) / 2 / systemZoom + dualScreenLeft;
			    const top = (height - h) / 2 / systemZoom + dualScreenTop;
			 		var url ='<?php echo esc_url_raw($this->connect_url); ?>';
			 		url = url.replace(/&amp;/g, '&');
			    const newWindow = window.open(url, "newwindow", config=      `scrollbars=yes,
			      width=${w / systemZoom}, 
			      height=${h / systemZoom}, 
			      top=${top}, 
			      left=${left},toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no
			      `);
			    if (window.focus) newWindow.focus();
				});

				//Step-1				
				jQuery(document).ready(function() {
					let tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
					if(tracking_option != ""){
						if(subscription_id != ""){
	        		call_list_analytics_web_properties(tracking_option, tvc_data);
	        	}
	        	jQuery(".slctunivr-filed").slideUp();
			      jQuery("#"+tracking_option).slideDown();
			      //is_validate_step("step_1");
	        }
	        var ua_page =2;
	        var ga4_page =2;
	        jQuery('.tvc-ua-option-more').click(function(event){
					  //let tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
					  call_list_analytics_web_properties("UA", tvc_data, ua_page); 
					   ua_page++;
					  event.stopPropagation();
					})
					jQuery('.tvc-ga4-option-more').click(function(event){
					  //let tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
					  call_list_analytics_web_properties("GA4", tvc_data, ga4_page); 
					   ga4_page++;
					  event.stopPropagation();
					})

		      jQuery("input[type=radio][name=analytic_tag_type]").on( "change", function() {
		      	let tracking_option = this.value;
		      	if(subscription_id != ""){
		        	call_list_analytics_web_properties(tracking_option, tvc_data);
		        	is_validate_step("step_1");
		        }
		        jQuery(".slctunivr-filed").slideUp();
		        jQuery("#"+tracking_option).slideDown();		        
		      });
		    });
				if(subscription_id != ""){
			  	call_list_googl_ads_account(tvc_data);
			  	call_list_google_merchant_account(tvc_data);
			  }

        //Step-2
        // create google ads account
        jQuery("#ads-continue").on('click', function(e){
		    	e.preventDefault();
		      create_google_ads_account(tvc_data);	
		      jQuery('.ggladspp').removeClass('showpopup');	      
		    });
		    // skip google ads account selection
		    jQuery("#ads-skip-continue").on('click', function(e){
          e.preventDefault();
          save_google_ads_data("", tvc_data, subscription_id, true );
         	go_next(jQuery("#step_2"));
        });
        //Step - 3
        jQuery("#create_merchant_account").on('click', function(e){
          e.preventDefault();
          create_google_merchant_center_account(tvc_data);
        });
        //Click skip merchant center account on popup
        jQuery("#merchant-center-skip-continue").on('click', function(e){
        	e.preventDefault();
        	if(is_validate_step("step_1")){
          	save_merchant_data("", "", tvc_data, subscription_id, plan_id, true );
          }else{
          	add_message("error","Please select property/measurement id.");
          }
        })
        //Click finish button
        jQuery("#step_3").on('click', function(e){
          e.preventDefault();
          let google_merchant_center_id = jQuery("#new_merchant_id").text();
          let merchant_id = "NewMerchant";
          if( google_merchant_center_id == null || google_merchant_center_id =="" ){
            google_merchant_center_id = jQuery('#google_merchant_center_id').val();
            merchant_id =jQuery("#google_merchant_center_id").find(':selected').data('merchant_id');
          }
          if( google_merchant_center_id == null || google_merchant_center_id == "" ){
          	jQuery('#tvc_merchant_center_skip_confirm').addClass('showpopup');
						jQuery('body').addClass('scrlnone');
          }else{
          	if(is_validate_step("step_1")){
            	save_merchant_data(google_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, false );
            }else{
          		add_message("error","Please Connect Google Analytics Account.");
          	}
          }
        })
        //Click confirm button on confirm popup
        jQuery('#confirm_selection').on('click', function(e){
          var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
          var tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
          var view_id = "";
          add_message("warning","Processing... Do not refresh.",false);
          if(tracking_option == "UA"){
          	ga_view_id = jQuery("#ua_web_property_id").find(':selected').data('profileid');
          }else{
          	ga_view_id = jQuery("#both_web_property_id").find(':selected').data('profileid');
          }
          jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {action: "update_setup_time_to_subscription", tvc_data:tvc_data, subscription_id:subscription_id, ga_view_id:ga_view_id, conversios_onboarding_nonce:conversios_onboarding_nonce},
            beforeSend: function () {
              loaderSection(true);
            },
            success: function (response) {
            	//console.log(response);
              if (response.error === false) {  
              	if(response.return_url){
              		location.replace( response.return_url);
              	}else{           
                	location.replace( "admin.php?page=conversios-google-analytics"); 
                }              
              }else{
                loaderSection(false);
              }
            }
          });
        });

				/**
				 * Convesios defoult html script
				 */
				 jQuery(document).ready(function() {
			    jQuery( ".stepdtltop" ).each(function() {
			        jQuery(this).on("click", function(){
			        	if(subscription_id != ""){
			        		if(jQuery(this).attr("data-is-done") == "1"){
			        			if(jQuery(this).parent('.onbordording-step').hasClass("activestep")){
			        				jQuery(this).parent('.onbordording-step').removeClass('activestep');
			        			}else{
			          			jQuery('.onbordording-step').removeClass('activestep');
			          			jQuery(this).parent('.onbordording-step').addClass('activestep');
			          		}
			          	}
			          }else{
					    		//alert("First Connect you website.");
					    	}
			        });
			    });

			    jQuery( ".stpnxttrgr" ).each(function() {
			      jQuery(this).on("click", function(event){			      	
				      	var step =jQuery(this).attr("id");	
				      	//step 1 next button call			    
						    if(step == "step_1"){
						    	if(is_validate_step(step)){
						        let tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
						        save_analytics_web_properties( tracking_option, tvc_data, subscription_id );
						        go_next(this);
						        call_list_google_merchant_account(tvc_data);
						      }
						    }
						    //step 2 next button call
						    if(step == "step_2" ){
						    	//event.preventDefault();
					      	let google_ads_id = jQuery("#new_google_ads_id").text();
			            if(google_ads_id ==null || google_ads_id ==""){
			              google_ads_id = jQuery('#ads-account').val();
			            }
			            let tr_ads = save_google_ads_data(google_ads_id, tvc_data, subscription_id, false );
			            if(tr_ads){			            	
			            	go_next(this);
			            }		            
					      }					      
			          
			       });
			    });

			  });
		    jQuery('.slctunivr-filed').slideUp();
		    //
		    
		    function go_next(next_this){
		    	jQuery(next_this).closest('.onbordording-step').find('.stepdtltop').attr("data-is-done","1");
		    	jQuery(next_this).closest('.onbordording-step').addClass('selectedactivestep');
		      jQuery(next_this).closest('.onbordording-step').removeClass('activestep');
		      jQuery( next_this ).closest('.onbordording-step').next('.onbordording-step').addClass('activestep');
		    }
		</script>
		<script>
		  jQuery(document).ready(function(){
		    jQuery(".slect2bx").select2();
		  });
		</script>
		<!-- popup script -->
		<script>
    jQuery(document).ready(function() {
    	//open now google ads account popup
      jQuery(".newggladsbtn").on( "click", function() {
          jQuery('.ggladspp').addClass('showpopup');
          jQuery('body').addClass('scrlnone');
      });
      
      //close any poup whie click on out side
      jQuery('body').click(function(evt){    
        if(jQuery(evt.target).closest('#step_2,.cretnewbtn,.finishbtn,.onbrdnpp-cntner, .crtemrchntpp .onbrdppmain').length)
        return;
          jQuery('.onbrd-popupwrp').removeClass('showpopup');
          jQuery('body').removeClass('scrlnone');
        });
      });
      jQuery(".clsbtntrgr, .ppblubtn").on( "click", function() {
          jQuery(this).closest('.onbrd-popupwrp').removeClass('showpopup');
          jQuery('body').removeClass('scrlnone');
      });
      /*
      jQuery(".sndinvitebtn").on( "click", function() {
          
          //jQuery('.acccretpp').addClass('showpopup');
          //jQuery('body').addClass('scrlnone');
      });
      jQuery(".finishbtn").on( "click", function() {
          jQuery('.congratepp').addClass('showpopup');
          jQuery('body').addClass('scrlnone');
          jQuery('.alertbx').removeClass('show');
      });*/
      jQuery(".newmrchntbtn").on( "click", function() {
          jQuery('.crtemrchntpp').addClass('showpopup');
          jQuery('body').addClass('scrlnone');
      });
      /*jQuery(".cretemrchntbtn").on( "click", function() {
          jQuery('.mrchntalert').addClass('show');
      });
      jQuery(".alertclsbtn").on( "click", function() {
          jQuery(this).parent('.alertbx').removeClass('show');
      });*/
			</script>
			<?php
		}
		/**
		 * onboarding page add scripts file
		 */
		public function add_scripts(){
			if(isset($_GET['page']) && sanitize_text_field($_GET['page']) == "conversios_onboarding"){
				wp_register_style('conversios-select2-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/select2.css'));
				wp_enqueue_style('conversios-style-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/style.css'), array(), $this->version, 'all');
				wp_enqueue_style('conversios-responsive-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/responsive.css'), array(), esc_attr($this->version), 'all');		
				wp_enqueue_style('conversios-select2-css');

				wp_register_script('conversios-select2-js', esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/js/select2.min.js') );
				wp_enqueue_script('conversios-select2-js');
				wp_enqueue_script( 'conversios-onboarding-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/onboarding-custom.js') , array( 'jquery' ), esc_attr($this->version), false );
			}
		}
		/**
		 * Onboarding page register menu
		 */
		public function register() {
			// Getting started - shows after installation.
			if(isset($_GET['page']) && sanitize_text_field($_GET['page']) == "conversios_onboarding"){
				add_dashboard_page(
					esc_html__( 'Welcome to Conversios Onboarding', 'conversios' ),
					esc_html__( 'Welcome to Conversios Onboarding', 'conversios' ),
					apply_filters( 'conversios_welcome', 'manage_options' ),
					'conversios_onboarding',
					array( $this, 'welcome_screen' )
				);
			}
		}
		/**
		 * Check if we should do any redirect.
		 */
		public function maybe_redirect() {
			if ( ! get_transient( '_conversios_activation_redirect' ) || isset( $_GET['conversios-redirect'] ) ) {
				return;
			}
			// Delete the redirect transient.
			delete_transient( '_conversios_activation_redirect' );
			
			if ( isset( $_GET['activate-multi'] ) ) { 
				return;
			}			
			
			$path = '?page=conversios_onboarding';
			$redirect = admin_url( $path );
			wp_safe_redirect( $redirect );
			exit;
			
		}		
		//End function
	}//End Conversios_Onboarding Class
} 
new Conversios_Onboarding();