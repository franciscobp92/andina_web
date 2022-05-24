<?php
class TVC_Pricings {
  protected $TVC_Admin_Helper="";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
  protected $pro_plan_site;

  public function __construct() {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data(); 
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->pro_plan_site = $this->TVC_Admin_Helper->get_pro_plan_site();     
    $this->create_form();
  }

  public function create_form() {       
    $googleDetail = [];
    $plan_id = 1;
    if(isset($this->google_detail['setting'])){
      if ($this->google_detail['setting']) {
        $googleDetail = $this->google_detail['setting'];
        if(isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))){
          $plan_id = $googleDetail->plan_id;
        }
      }
    }

    $close_icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close.png');
    $check_icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check.png');
    ?>
<div class="tab-content">
	<div class="tab-pane show active" id="tvc-account-page">
		<div class="tab-card" >			
       <div class="tvc-price-table-features columns-5">
        <div class="tvc-container"> 
          <div class="clearfix">
            <div class="row-heading clearfix">
               <div class="column tvc-blank-col"><span><?php esc_html_e("Features","conversios"); ?></span></div>
               <div class="column discounted tvc-free-plan">
                  <div class="name-wrap"><div class="name"><?php esc_html_e("STARTER","conversios"); ?></div></div>
                  <div class="tvc-list-price">
                    <div class="price-current"><span class="inner"><?php esc_html_e("FREE","conversios"); ?></span></div>
                    <div class="tvc_month_free"><?php esc_html_e("FOREVER FREE","conversios"); ?></div>
                  </div>
                  <span>
                    <a href="javascript:void(0)" class="btn tvc-btn"><?php esc_html_e("Currently Active","conversios"); ?></a>
                  </span>
               </div>
               <div class="column discounted ">
                  <div class="name-wrap"><div class="name"><?php esc_html_e("HUSTLE","conversios"); ?></div></div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal">
                        <span><?php esc_html_e("$39.00","conversios"); ?></span>
                        <div class="tvc-plan-off"><?php esc_html_e("50% OFF","conversios"); ?></div>
                      </div>
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$19"),esc_html_e("/month")); ?></span></div>
                      <div class="tvc_month_free"><?php esc_html_e("Limited Offer","conversios"); ?></div>
                    </div>
                    <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=plan_1_m&utm_source=EE+Plugin+User+Interface&utm_medium=HUSTLE&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","conversios"); ?></a>
                  </div>              
               </div>
               <div class="column discounted popular">
                <div class="tvc_popular">
                  <div class="tvc_popular_inner"><?php esc_html_e("POPULAR","conversios"); ?></div>
                </div>
                  <div class="name-wrap">
                     <div class="name"><?php esc_html_e("GROWTH","conversios"); ?></div>                
                  </div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal"><span><?php esc_html_e("$59.00","conversios"); ?></span>
                        <div class="tvc-plan-off"><?php esc_html_e("50% OFF","conversios"); ?></div>
                      </div>
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$29"),esc_html_e("/month")); ?></span></div>
                      <div class="tvc_month_free"><?php esc_html_e("Limited Offer","conversios"); ?></div>
                    </div>
                    <a target="_blank"  href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=plan_2_m&utm_source=EE+Plugin+User+Interface&utm_medium=GROWTH&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","conversios"); ?></a>
                  </div>              
               </div>
               <div class="column discounted ">
                  <div class="name-wrap">
                     <div class="name"><?php esc_html_e("LEAP","conversios"); ?></div>                
                  </div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal"><span><?php esc_html_e("$99.00","conversios"); ?></span>
                        <div class="tvc-plan-off"><?php esc_html_e("50% OFF","conversios"); ?></div>
                      </div>
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$49"),esc_html_e("/month")); ?></span></div>
                      <div class="tvc_month_free"><?php esc_html_e("Limited Offer","conversios"); ?></div>
                    </div>
                    <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=plan_3_m&utm_source=EE+Plugin+User+Interface&utm_medium=LEAP&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","conversios"); ?></a>
                  </div>                
               </div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Analytics","conversios"); ?></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Universal Analytics Tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Analytics 4 Tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dual Set up (UA + GA4)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("eCommerce tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Shopping Behavior Analysis","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Checkout Behavior Tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Channel Performance Analysis","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("All Pages tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Analytics and Google Ads linking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Custom dimensions tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Custom metrics tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Affiliate performance tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Coupon Performance Tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Actionable Dashboard (GA3/ GA4)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Facebook pixel tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Google Shopping","conversios"); ?></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Merchant Center account management","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Site verification","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Domain claim","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Products Sync via Content API","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 100)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 1000)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 5000)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Automatic Products Update","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 100)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 1000","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 5000)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Schedule Product Sync","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Smart Shopping Campaign management","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Smart Shopping reports","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads and Google Merchant Center account linking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Remarketing tags","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dynamic Remarketing Tags for eCommerce events","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","conversios"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads Conversion tracking","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Compatibility with Brands Plugin","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Product filters for selected products sync","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Performance max campaigns","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","conversios"); ?></div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Support","conversios"); ?></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Free Google Analytics Audit","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Free Consultation with Shopping Expert","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dedicated Customer Success Manager","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-footer clearfix">
               <div class="column"><?php esc_html_e("Premium Support (24*7)","conversios"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="tvc-guarantee">
        <div class="guarantee">
          <div class="title"><?php printf("<span>%s</span>%s", esc_html_e("15 Days","conversios"), esc_html_e("100% No-Risk Money Back Guarantee!","conversios")); ?></div>
          <div class="description"><?php esc_html_e("You are fully protected by our 100% No-Risk-Double-Guarantee. If you don’t like over the next 15 days, then we will happily refund 100% of your money. No questions asked.","conversios"); ?></div>
        </div>
      </div>
    </div>
	</div>
</div>
<?php
    }
}
?>