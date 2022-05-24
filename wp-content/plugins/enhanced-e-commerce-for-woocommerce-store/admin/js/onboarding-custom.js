$=jQuery;
function loaderSection(isShow) {
  if (isShow){
    jQuery('#loader-section').show();
  }else{
    jQuery('#loader-section').hide();
  }
}
var tvc_time_out="";
function add_message(type, msg, is_close = true){
  let tvc_popup_box = document.getElementById('tvc_onboarding_popup_box');
  tvc_popup_box.classList.remove("tvc_popup_box_close");
  tvc_popup_box.classList.add("tvc_popup_box");
  if(type == "success"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-success'>"+msg+"</div>";
  }else if(type == "error"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-error'>"+msg+"</div>";
  }else if(type == "warning"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-warning'>"+msg+"</div>";
  }
  if(is_close){
    tvc_time_out = setTimeout(function(){  //tvc_popup_box.style.display = "none";       
      tvc_popup_box.classList.add("tvc_popup_box_close");
      tvc_popup_box.classList.remove("tvc_popup_box");        
    }, 4000);
  } 
}

function is_validate_step(step){
  var is_valide = false;
  if(step == "step_1"){
    var web_property_id = ""; var ua_account_id = ""; var web_measurement_id = ""; var ga4_account_id = "";
    var tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
    //console.log(tracking_option);
    if(tracking_option == "UA"){
      web_property_id = jQuery('#ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#ua_web_property_id_option_val").data('accountid');
      if(web_property_id == undefined || web_property_id == ""){        
        msg = "Please select web property id.";        
      }else{
        is_valide = true;        
      }
    }else if(tracking_option == "GA4"){
      web_measurement_id = jQuery('#ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#ga4_web_measurement_id_option_val").data('accountid');
      if(web_measurement_id == undefined || web_measurement_id == ""){
        msg = "Please select measurement id.";
      }else{
        is_valide = true;
      }
    }else{
      web_property_id = jQuery('#both_ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#both_ua_web_property_id_option_val").data('accountid');
      web_measurement_id = jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#both_ga4_web_measurement_id_option_val").data('accountid');
      //console.log(web_property_id+"=="+web_measurement_id);
      if((web_property_id == undefined || web_property_id == "") || (web_measurement_id == undefined || web_measurement_id == "") ){
        msg = "Please select property/measurement id.";
      }else{
        is_valide = true;
      }
    }
    //console.log("is_valide"+is_valide+"-"+tracking_option+"-"+web_property_id);
    if(is_valide){
      jQuery('#step_1').prop('disabled', false);
    }else{
      jQuery('#step_1').prop('disabled', true);
    }
  }else if(step == "step_2"){
    google_ads_id = jQuery('#ads-account').val();
    if(google_ads_id == ""){
      msg = "Please select Google Ads account.";
    }else{
      is_valide = true;
    }
    if(is_valide){
      jQuery('#step_2').prop('disabled', false);
    }else{
      jQuery('#step_2').prop('disabled', true);
    }
    
  }
  return is_valide;
}
jQuery(document).ready(function () {
  loaderSection(false);
  //step-1
  jQuery('.tvc-dropdown-header').click(function(event){
    jQuery(this).next().toggle();
    event.stopPropagation();
  })

  jQuery(window).click(function(){
      jQuery('.tvc-dropdown-content').hide();
  })
  jQuery(document.body).on('click', 'option:not(.more)', function(event){
      //alert('clicked option ' + this.innerHTML);

      var option_id = jQuery(this).parent().parent().attr("id");
      //console.log(option_id);
      var val = jQuery(this).attr("value");
      var accountid = jQuery(this).attr("data-accountid");
      
      var text = jQuery(this).html();
      let tracking_option = jQuery('input:radio[name=analytic_tag_type]:checked').val();

      if(tracking_option == "UA" || (tracking_option == "BOTH" && option_id == "both_ua_web_property_id_option")){
        var profileid = jQuery(this).attr("data-profileid");
        profileid = (profileid == undefined)?"":profileid;
        accountid = (accountid == undefined)?"":accountid;
        //console.log(accountid+"="+profileid);

        jQuery("#"+option_id+"_val").html(text);
        jQuery("#"+option_id+"_val").attr("data-accountid",accountid);
        jQuery("#"+option_id+"_val").attr("data-profileid",profileid);
        jQuery("#"+option_id+"_val").attr("data-val",val);

      }else if(tracking_option == "GA4" || (tracking_option == "BOTH" && option_id == "both_ga4_web_measurement_id_option") ){
        var name = jQuery(this).attr("data-name");
        name = (name == undefined)?"":name;
        accountid = (accountid == undefined)?"":accountid;
        jQuery("#"+option_id+"_val").html(text);
        jQuery("#"+option_id+"_val").attr("data-accountid",accountid);
        jQuery("#"+option_id+"_val").attr("data-name",name);
        jQuery("#"+option_id+"_val").attr("data-val",val);
      }
      jQuery(this).parent().parent().toggle();
      validate_google_analytics_sel();
      event.stopPropagation();
  })

/*jQuery(".google_analytics_sel").on( "change", function() {
    is_validate_step("step_1");
    jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
    jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  }); */
  //step-2
  jQuery(".google_ads_sel").on( "change", function() {
    //jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
    //jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  }); 
  jQuery('input[type=checkbox]:not(#adult_content, #terms_conditions)').change(function() {
    //jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
   // jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  });
  
  //select2
  //jQuery(".select2").select2();
  // desable to close advance settings
  jQuery(".advance-settings .dropdown-menu").click(function(e){
      e.stopPropagation();
  });
});
function validate_google_analytics_sel(){
  is_validate_step("step_1");
  jQuery(".onbrdstep-1").removeClass('selectedactivestep');
  jQuery(".onbrdstep-3").removeClass('selectedactivestep');
  jQuery(".onbrdstep-2").removeClass('selectedactivestep');
  jQuery("[data-id=step_1]").attr("data-is-done",0);
  jQuery("[data-id=step_2]").attr("data-is-done",0);
  jQuery("[data-id=step_3]").attr("data-is-done",0);
}
function is_change_analytics_account(){
  let tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
  let old_tracking_option = jQuery('#old_tracking').attr("data-tracking_option");
  if(tracking_option != old_tracking_option){
    return true;
  }else if(tracking_option == "UA"){
    let web_property_id = jQuery('#ua_web_property_id_option_val').attr("data-val");
    let old_web_property_id = jQuery('#old_tracking').attr("data-property_id");
    if(web_property_id != old_web_property_id){
      return true;
    }else{
      return false;
    }
  }else if(tracking_option == "GA4"){
    let web_measurement_id = jQuery('#ga4_web_measurement_id_option_val').attr("data-val");
    let old_web_measurement_id = jQuery('#old_tracking').attr("data-measurement_id");
    if(web_measurement_id != old_web_measurement_id){
      return true;
    }else{
      return false;
    }
  }else if(tracking_option == "BOTH"){
    let web_property_id = jQuery('#both_ua_web_property_id_option_val').attr("data-val");
    let web_measurement_id = jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val");
    let old_web_property_id = jQuery('#old_tracking').attr("data-property_id");
    let old_web_measurement_id = jQuery('#old_tracking').attr("data-measurement_id");
    if(web_measurement_id != old_web_measurement_id || web_property_id != old_web_property_id){
      return true;
    }else{
      return false;
    }
  }
}
//save nalytics web properties while next button click
function save_analytics_web_properties(tracking_option, tvc_data, subscription_id){

  if(subscription_id != "" && is_change_analytics_account()){
    var web_measurement_id = "";
    var web_property_id = "";
    var ga4_account_id = "";
    var ua_account_id = "";
    var is_valide = true;
    var msg ="";
    if(tracking_option == "UA"){
      web_property_id = jQuery('#ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#ua_web_property_id_option_val").attr('data-accountid');
      if(web_property_id == ""){
        is_valide = false;
        msg = "Please select web property id.";
      }
    }else if(tracking_option == "GA4"){
      web_measurement_id = jQuery('#ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#ga4_web_measurement_id_option_val").attr('data-accountid');
      if(web_measurement_id == ""){
        is_valide = false;
        msg = "Please select measurement id.";
      }
    }else{
      web_property_id = jQuery('#both_ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#both_ua_web_property_id_option_val").attr('data-accountid');
      web_measurement_id = jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#both_ga4_web_measurement_id_option_val").attr('data-accountid');

      if(web_property_id == "" || web_measurement_id == ""){
        is_valide = false;
        msg = "Please select property/measurement id.";
      }
    }
    if(is_valide == true){
      var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
      var data = {
        action: "save_analytics_data",
        subscription_id:subscription_id,
        tracking_option: tracking_option,
        web_measurement_id: web_measurement_id,
        web_property_id: web_property_id,
        ga4_account_id: ga4_account_id,
        ua_account_id: ua_account_id,
        enhanced_e_commerce_tracking: jQuery('#enhanced_e_commerce_tracking').is(':checked'),
        user_time_tracking: jQuery('#user_time_tracking').is(':checked'),
        add_gtag_snippet: jQuery('#add_gtag_snippet').is(':checked'),
        client_id_tracking: jQuery('#client_id_tracking').is(':checked'),
        exception_tracking: jQuery('#exception_tracking').is(':checked'),
        enhanced_link_attribution_tracking: jQuery('#enhanced_link_attribution_tracking').is(':checked'),
        tvc_data:tvc_data,
        conversios_onboarding_nonce:conversios_onboarding_nonce
      };
      //console.log(data);
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: data,
        beforeSend: function(){
          loaderSection(true);
        },
        success: function(response){
          loaderSection(false);
          if (response.error === false) {          
            add_message("success","Google Analytics successfully updated.");
            return true;
          }else{
            add_message("error","Error while updating Google Analytics.");
            return false;
          }
          
        }
      });
      
    }else{
      add_message("warning",msg);
      return false;
    }
  }else if( subscription_id == "" ){
    add_message("warning","Missing value of subscription id.");
    return false;
  }  
}

function save_google_ads_data(google_ads_id, tvc_data, subscription_id, is_skip=false){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  if(google_ads_id || is_skip == true){
    loaderSection(true);
    var data = {
      action: "save_google_ads_data",
      subscription_id:subscription_id,
      google_ads_id: google_ads_id,
      remarketing_tags: jQuery('#remarketing_tag').is(':checked'),
      dynamic_remarketing_tags: jQuery('#dynamic_remarketing_tags').is(':checked'),
      google_ads_conversion_tracking: jQuery("#google_ads_conversion_tracking").is(':checked'),
      link_google_analytics_with_google_ads: jQuery("#link_google_analytics_with_google_ads").is(':checked'),
      tvc_data:tvc_data,
      conversios_onboarding_nonce:conversios_onboarding_nonce
    };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      beforeSend: function () {        
      },
      success: function (response) {
        //console.log(response);
        if(response.error === false) {
          add_message("success","Google Ads successfully updated.");
          //jQuery("#ads-account").val(google_ads_id);
          let tracking_option = jQuery('input:radio[name=analytic_tag_type]:checked').val();
          var s_tracking_option = tracking_option.toLowerCase();
          
          if (jQuery("#link_google_analytics_with_google_ads").is(':checked')) {         
            if(tracking_option == "UA" || tracking_option == "BOTH"){
              if(tracking_option == "BOTH"){
                s_tracking_option = "both_ua";
              }
              var profile_id = jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr('data-profileid');
              var UalinkData = {
                  action: "link_analytic_to_ads_account",
                  type: "UA",
                  ads_customer_id: google_ads_id,
                  analytics_id: jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr('data-accountid'),
                  web_property_id: jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr("data-val"),
                  profile_id: profile_id,
                  tvc_data:tvc_data,
                  conversios_onboarding_nonce:conversios_onboarding_nonce
              };
              //console.log("google_ads_id"+google_ads_id+"profile_id"+profile_id);
              //console.log(UalinkData);
              if(google_ads_id != "" && profile_id != undefined){
                setTimeout(function(){  
                  //link_analytic_to_ads_account(UalinkData);
                  jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: UalinkData,
                    success: function (response) {
                      clearTimeout(tvc_time_out);
                      if(response.error === false){
                        add_message("success","Google ananlytics and google ads linked successfully.");
                      }else{
                        //const errors = JSON.parse(response?.errors[0]);
                        //add_message("error",errors?.message);
                      }

                      /* start GA4 */
                      if(tracking_option == "GA4" || tracking_option == "BOTH"){
                        if(tracking_option == "BOTH"){
                          s_tracking_option = "both_ga4";
                        }
                        var web_property = jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr('data-name');
                        var Ga4linkData = {
                          action: "link_analytic_to_ads_account",
                          type: "GA4",
                          ads_customer_id: google_ads_id,
                          web_property_id: jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr("data-val"),
                          web_property: web_property,
                          tvc_data:tvc_data,
                          conversios_onboarding_nonce:conversios_onboarding_nonce
                        };
                        //console.log("web_property"+web_property);
                        if(google_ads_id != "" && web_property != undefined){
                          //setTimeout(function(){
                            //console.log("cal GA4 link");
                            //link_analytic_to_ads_account(Ga4linkData);
                            jQuery.ajax({
                              type: "POST",
                              dataType: "json",
                              url: tvc_ajax_url,
                              data: Ga4linkData,
                              success: function (response) {
                                console.log(response);
                                clearTimeout(tvc_time_out);
                                if(response.error === false){
                                  add_message("success","Google ananlytics and google ads linked successfully.");
                                }else{
                                  //const errors = JSON.parse(response?.errors[0]);
                                  //add_message("error",errors?.message);
                                }
                              }
                            });
                          //}, 1000); 
                        }
                      }
                      /* end GA4 */

                    }
                  });
                }, 1000); 
              }
              
            }else if(tracking_option == "GA4" || tracking_option == "BOTH"){
              if(tracking_option == "BOTH"){
                s_tracking_option = "both_ga4";
              }
              var web_property = jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr('data-name');
              var Ga4linkData = {
                action: "link_analytic_to_ads_account",
                type: "GA4",
                ads_customer_id: google_ads_id,
                web_property_id: jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr("data-val"),
                web_property: web_property,
                tvc_data:tvc_data,
                conversios_onboarding_nonce:conversios_onboarding_nonce
              };
              //console.log("web_property"+web_property);
              if(google_ads_id != "" && web_property != undefined){
                setTimeout(function(){
                  //console.log("cal GA4 link");
                  //link_analytic_to_ads_account(Ga4linkData);
                  jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: Ga4linkData,
                    success: function (response) {
                      console.log(response);
                      clearTimeout(tvc_time_out);
                      if(response.error === false){
                        add_message("success","Google ananlytics and google ads linked successfully.");
                      }else{
                        //const errors = JSON.parse(response?.errors[0]);
                        //add_message("error",errors?.message);
                      }
                    }
                  });
                }, 1000); 
              }            
              /* end GA4 */
            }
            
            if(plan_id != 1){
              setTimeout(function(){
                check_oradd_conversion_list(google_ads_id, tvc_data);
              }, 3500);
            }          
            loaderSection(false);
            return true;
          }            
        }else{
          add_message("error","Error while updating Google Ads.");
        }
        loaderSection(false);
      }
    });
    return true;
  }else{
    jQuery('#tvc_ads_skip_confirm').addClass('showpopup');
    jQuery('body').addClass('scrlnone');
    //jQuery('#tvc_ads_skip_confirm').modal('show');  
    return false;
  }
}
function save_merchant_data(google_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, is_skip=fals){
  if(google_merchant_center_id || is_skip == true){
    var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
    var website_url = jQuery("#url").val();
    var customer_id = jQuery("#loginCustomerId").val();
    var data = {
      action: "save_merchant_data",
      subscription_id:subscription_id,
      google_merchant_center:google_merchant_center_id,
      merchant_id: merchant_id,
      website_url:website_url,
      customer_id:customer_id,
      tvc_data:tvc_data,
      conversios_onboarding_nonce:conversios_onboarding_nonce
    };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      beforeSend: function () { 
        loaderSection(true);       
      },
      success: function (response) {
        let google_ads_id = jQuery("#new_google_ads_id").text();
        if(google_ads_id ==null || google_ads_id ==""){
          google_ads_id = jQuery('#ads-account').val();
        }
        
        if (response.error === false) {
          add_message("success","Google merchant center successfully updated.");
          //clearTimeout(tvc_time_out);
          var link_data = {
            action: "link_google_ads_to_merchant_center",
            account_id: google_merchant_center_id,
            merchant_id: merchant_id,
            adwords_id: google_ads_id,
            tvc_data:tvc_data,
            conversios_onboarding_nonce:conversios_onboarding_nonce
          };
          if(google_merchant_center_id != "" && google_ads_id != ""){
            link_google_Ads_to_merchant_center(link_data, tvc_data, subscription_id);
          }else{
            get_subscription_details(tvc_data, subscription_id); 
          }
        } else {         
          add_message("error","Error while updating Google merchant center.");
        }

        //loaderSection(false);        
      }
    });    
  }else{
    add_message("warning","Missing Google merchant center accountid.");
  }
}

/* get conversion list */
function check_oradd_conversion_list(google_ads_id,  tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  if(google_ads_id ){
    var data = {
        action: "get_conversion_list",
        customer_id:google_ads_id,
        tvc_data:tvc_data,
        conversios_onboarding_nonce:conversios_onboarding_nonce
      };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      success: function (response) {
        //console.log(response);
        clearTimeout(tvc_time_out);
        if(response.error === false){
          setTimeout(function(){
            add_message("success",response.message);
          }, 2000);
        }else{
          //const errors = JSON.parse(response.errors[0]);
          if(response.errors){
            setTimeout(function(){
              add_message("error",response.errors);
            }, 2000);
          }
          
        }
      }
    });
  }
}
/* link account code */
/*function link_analytic_to_ads_account(data) {
  $.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: data,
    success: function (response) {
      console.log(response);
      clearTimeout(tvc_time_out);
      if(response.error === false){
        add_message("success","Google ananlytics and google ads linked successfully.");
      }else{
        //const errors = JSON.parse(response?.errors[0]);
        //add_message("error",errors?.message);
      }
    }
  });
}*/

function link_google_Ads_to_merchant_center(link_data, tvc_data, subscription_id){
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: link_data,
    beforeSend: function(){
      //loaderSection(true);
    },
    success: function (response) {
      clearTimeout(tvc_time_out);
      if(response.error === false){        
        add_message("success",response.data.message);
      }else if(response.error == true && response.errors != undefined){
        const errors = JSON.parse(response.errors[0]);
        add_message("error",errors.message);
      }else{
        add_message("error","There was an error while link account");
      }
      get_subscription_details(tvc_data, subscription_id);  
      //loaderSection(false);      
    }
  });
}
/* get subscription details */
function get_subscription_details(tvc_data, subscription_id) { 
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val(); 
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "get_subscription_details", tvc_data:tvc_data, subscription_id:subscription_id, conversios_onboarding_nonce:conversios_onboarding_nonce},
    beforeSend: function () {
    },
    success: function (response) {
      if (response.error === false) {
        jQuery("#google_analytics_property_id_info").hide();
        jQuery("#google_analytics_measurement_id_info").hide();
        jQuery("#google_ads_info").hide();
        jQuery("#google_merchant_center_info").hide();
        if(response.data.property_id != ""){
          jQuery("#selected_google_analytics_property").text(response.data.property_id);
          jQuery("#google_analytics_property_id_info").show();
        }
        if(response.data.measurement_id != ""){
          jQuery("#selected_google_analytics_measurement").text(response.data.measurement_id);
          jQuery("#google_analytics_measurement_id_info").show();
        }
        if(response.data.google_ads_id != ""){
          jQuery("#selected_google_ads_account").text(response.data.google_ads_id);
          jQuery("#google_ads_info").show();
        }
        if(response.data.google_merchant_center_id != ""){
          jQuery("#selected_google_merchant_center").text(response.data.google_merchant_center_id);
          jQuery("#google_merchant_center_info").show();
        } 
        jQuery('#tvc_confirm_submite').addClass('showpopup');
        jQuery('body').addClass('scrlnone');       
        //jQuery('#tvc_confirm_submite').modal('show');
      } else {
        add_message("error","Error while fetching subscription data");
      } 
      loaderSection(false);
    }
  });
}
/* List function */
//call get list propertie function base on tracking_option
function call_list_analytics_web_properties(tracking_option, tvc_data, page =1){
  if (tracking_option == 'UA'){
    let web_property_id_length = jQuery('#ua_web_property_id_option option').length;
    if(web_property_id_length < 2 || page != 1){
      list_analytics_web_properties("UA", tvc_data, page);
    }else if( page != 1){
      list_analytics_web_properties("UA", tvc_data, page);
    }
  }else if (tracking_option == 'GA4' ){
    let web_measurement_id_length = jQuery('#ga4_web_measurement_id_option option').length;
    if(web_measurement_id_length < 2){
      list_analytics_web_properties("GA4", tvc_data, page);
    }else if( page != 1){
      list_analytics_web_properties("GA4", tvc_data, page);
    }     
  }else if (tracking_option == 'BOTH'){
    let web_property_id_length = jQuery('#both_ua_web_property_id_option option').length;
    let web_measurement_id_length = jQuery('#both_ga4_web_measurement_id_option option').length;
    if(web_measurement_id_length < 2 && web_property_id_length < 2){
      list_analytics_web_properties("BOTH", tvc_data);
    }else if(web_property_id_length < 2 ){
      list_analytics_web_properties("UA", tvc_data);
    }else if(web_measurement_id_length < 2 ){
      list_analytics_web_properties("GA4", tvc_data);
    }

  }
}
// get list properties dropdown options
function list_analytics_web_properties(type, tvc_data, page =1) {
  loaderSection(true);
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  //console.log("page"+page);
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "get_analytics_web_properties", type: type, page:page, tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
        //console.log(response);
     var s_tracking_option = type.toLowerCase()
      if (response != null && response.error == false) {
        if (type == "UA" || type == "BOTH") {
          //web_properties_dropdown
          var subscriptionPropertyId = jQuery("#subscriptionPropertyId").val();
          var ga_view_id = jQuery("#ga_view_id").val();
          var PropOptions = '';
          
          //console.log("call option");
          //console.log(Object.keys(response.data.wep_properties).length +"=="+response.data.wep_properties.length);
          if(response.data != null && response.data.wep_properties.length > 0){
            jQuery.each(response.data.wep_properties, function (propKey, propValue) {
                var selected ="";              
                if (subscriptionPropertyId == propValue.webPropertyId) {
                  if(ga_view_id != "" && ga_view_id == propValue.id){
                    selected = "selected='selected'";
                  }else if(ga_view_id =="" ){
                    selected = "selected='selected'";
                  }
                  /*if(type == "BOTH"){
                    s_tracking_option = "both_ua";
                  }*/
                  jQuery("#both_ua_web_property_id_option_val").attr("data-profileid",propValue.id); 
                  jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr("data-profileid",propValue.id);                      
                }else{
                  selected = "";
                }
                PropOptions = PropOptions + '<option value="' + propValue.webPropertyId + '" ' + selected + ' data-accountid="' + propValue.accountId + '" data-profileid="' + propValue.id + '"> ' + propValue.accountName + ' - ' + propValue.propertyName + ' - ' + propValue.name + ' - ' + propValue.webPropertyId +'</option>';
            });
          }else{
            //console.log("hide option");
            if(page == 1){
              list_analytics_web_properties(type, tvc_data, 2);
              return;
            }
            jQuery(".tvc-ua-option-more").hide();
          }
          jQuery('#ua_web_property_id_option > .tvc-select-items').append(PropOptions);
          jQuery('#both_ua_web_property_id_option > .tvc-select-items').append(PropOptions);
        }
        if (type == "GA4" || type == "BOTH") {
          //web_measurement_dropdown
          var subscriptionMeasurementId = jQuery("#subscriptionMeasurementId").val();
          var MeasOptions = '';
          if(response.data != null && response.data.wep_measurement.length > 0){
            jQuery.each(response.data.wep_measurement, function (measKey, measValue) {
              var web_property = measValue.name.split("/");
              if (subscriptionMeasurementId == measValue.measurementId) {
                var selected = "selected='selected'";
                /*if(type == "BOTH"){
                  s_tracking_option = "both_ga4";
                };*/
                jQuery("#both_ga4_web_measurement_id_option_val").attr('data-name',web_property[1]);
                jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr('data-name',web_property[1]);
              } else {
                var selected = "";
              }              
              MeasOptions = MeasOptions + '<option value="' + measValue.measurementId + '" ' + selected + ' data-name="'+web_property[1] +'"'+ ' data-accountid="' + measValue.accountId + '"> ' + measValue.accountName + ' - ' + web_property[1] + ' - ' + measValue.measurementId + '</option>';
            });
          }else{
            //console.log("hide option");
            if(page == 1){
              list_analytics_web_properties(type, tvc_data, 2);
              return;
            }
            jQuery(".tvc-ga4-option-more").hide();
          }
          jQuery('#ga4_web_measurement_id_option > .tvc-select-items').append(MeasOptions);
          jQuery('#both_ga4_web_measurement_id_option > .tvc-select-items').append(MeasOptions);
        }
        jQuery(".slect2bx").select2();
      }else if( response != null && response.error == true && response.errors != undefined){
        const errors = response.errors[0];
        add_message("error",errors);
      }else{
      add_message("error","It seems "+type +" Google analytics account not fetched");
    }
      is_validate_step("step_1");
      loaderSection(false);
    }
  });
}
function call_list_googl_ads_account(tvc_data){
  let ads_account_length = jQuery('#ads-account option').length;
  if(ads_account_length < 2){
    list_googl_ads_account(tvc_data);
  }
}
// get list google ads dropdown options
function list_googl_ads_account(tvc_data) {
  //loaderSection(true);
  var selectedValue = jQuery("#subscriptionGoogleAdsId").val();
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "list_googl_ads_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
      if (response.error === false) {
        jQuery('#ads-account').empty();
        jQuery('#ads-account').append(jQuery('<option>', {
            value: "",
            text: "Select Google Ads Account"
        }));
        if (response.data.length == 0) {
          add_message("warning","There are no Google ads accounts associated with email.");
        } else {
          if(response.data.length > 0){
            jQuery.each(response.data, function (key, value) {
              if (selectedValue == value) {
                jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,selected: "selected"}));
              } else {
                if(selectedValue == "" && key == 0){                
                  jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,selected: "selected"}));
                }else{
                  jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,}));
                }
              }
            });
          }
        }
      } else {
        add_message("warning","There are no Google ads accounts associated with email.");
      }
      //loaderSection(false);
    }
  });
}

function call_list_google_merchant_account(tvc_data){
  let mcc_account_length = jQuery('#google_merchant_center_id option').length;
  if(mcc_account_length < 2){
    list_google_merchant_account(tvc_data);
  }
}
function list_google_merchant_account(tvc_data){
  var selectedValue = jQuery("#subscriptionMerchantCenId").val();
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "list_google_merchant_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
      if (response.error === false){
        jQuery('#google_merchant_center_id').empty();
        jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: "", text: "Select Google Merchant Center"}));
        if (response.data.length > 0) {        
          jQuery.each(response.data, function (key, value) {
            if(selectedValue == value.account_id){
              jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id,selected: "selected"}));
            }else{
              if(selectedValue == "" && key == 0){ 
                jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id,selected: "selected"}));
              }else{
                jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id,"data-merchant_id": value.merchant_id, text: value.account_id, }));
              }
            }
          });
        }else{
          add_message("error","There are no Google merchant center accounts associated with email.");
        }
      }else{
        add_message("error","There are no Google merchant center accounts associated with email.");
      }       
    }
  });
  loaderSection(false);
}
/* Create function */
function create_google_ads_account(tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "create_google_ads_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    beforeSend: function () {
      loaderSection(true);
    },
    success: function (response) {
      if (response.error === false) {
        add_message("success",response.data.message);
        jQuery("#new_google_ads_id").text(response.data.adwords_id);
        if(response.data.invitationLink != ""){
          jQuery("#ads_invitationLink").attr("href",response.data.invitationLink);
        }else{
          jQuery("#invitationLink").html("");
        }
        
        jQuery("#tvc_ads_section").slideUp();
        jQuery("#new_google_ads_section").slideDown();
        //localStorage.setItem("new_google_ads_id", response.data.adwords_id);
        //listGoogleAdsAccount();
      } else {
        add_message("error",response.data.message);
      }
      loaderSection(false);
    }
  });
}

function create_google_merchant_center_account(tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  var is_valide = true;
  var website_url = jQuery("#url").val();
  var email_address = jQuery("#get-mail").val();
  var store_name = jQuery("#store_name").val();
  var country = jQuery("#selectCountry").val();
  var customer_id = jQuery("#loginCustomerId").val();
  var adult_content = jQuery("#adult_content").is(':checked');
  if(website_url == ""){
    add_message("error","Missing value of website url.");
    is_valide = false;
  }else if(email_address == ""){
    add_message("error","Missing value of email address.");
    is_valide = false;
  }else if(store_name == ""){
    add_message("error","Missing value of store name.");
    is_valide = false;
  }else if(country == ""){
    add_message("error","Missing value of country.");
    is_valide = false;
  } else if(jQuery('#terms_conditions').prop('checked') == false){
    add_message("error","Please I accept the terms and conditions.");
    is_valide = false;
  }
  if(is_valide == true){
    var data = {
      action: "create_google_merchant_center_account",
      website_url: website_url,
      email_address: email_address,
      store_name: store_name,
      country: country,
      concent: 1,
      customer_id: customer_id,
      adult_content:adult_content,
      tvc_data:tvc_data,
      conversios_onboarding_nonce:conversios_onboarding_nonce
    };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      beforeSend: function () {
        loaderSection(true);
      },
      success: function (response, status) {
        if (response.error === false || response.merchant_id != undefined) {
          add_message("success","New merchant center created successfully.");              
          jQuery("#new_merchant_id").text(response.account.id);
          jQuery("#tvc_merchant_section").slideUp();
          jQuery("#new_merchant_section").slideDown();
        } else if (response.error === true) {
          const errors = JSON.parse(response.errors[0]);
          add_message("error",errors.message);
        } else {
          add_message("error","There was error to create merchant center account");
        }
        jQuery("#createmerchantpopup").removeClass('showpopup');
        jQuery('body').removeClass('scrlnone');
        //jQuery("#merchantconfirmModal").modal('hide');
        loaderSection(false);
      }
    });
    
  }
}