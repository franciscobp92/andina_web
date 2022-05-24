<div class="notification"></div>

<style type="text/css">
	a{
		cursor: pointer;
	}
	a.wc-storage-delete{
		color: #a00;
	}
	.submit .woocommerce-save-button{
		display: none;
	}
    .wc-payment-method-class-add, .wc-payment-method-class-delete, .wc-payment-method-class-save{
        font-size: 11px;
        padding: 10px 15px;
    }
    /*QuickReset*/
    *{margin:0;box-sizing:border-box;}
    html,body{height:100%;font:14px/1.4 sans-serif;}
    input, textarea{font:14px/1.4 sans-serif;}

    .input-group{
    display: table;
    border-collapse: collapse;
    width:100%;
    }
    .input-group > div{
    display: table-cell;
    vertical-align: middle;  /* needed for Safari */
    }
    .input-group-icon{
    background:transparent;
    color: #777;
    padding: 0 12px
    }
    .input-group-area{
    width:100%;
    }
    .input-group input{
    border: 0;
    display: block;
    width: 100%;
    padding: 8px;
    border-bottom: 1px solid #f2f2f2;

    }
    .select-expiration{
        width: 70px;
        border: transparent;
        border-radius: 0;
        padding: 0 5px;
    }
    .message{
        margin-top: 9px;
    }
    .message > p{
        font-size: 11px;
    }
</style>
<script>
    var isValidInfo = false;
    var isValidCVV = false;
	jQuery(
		function(){
            jQuery(document).ready(function() {
                jQuery(".woocommerce-Message").remove()
                
                jQuery('#add_payment_method').click(function (e) {
                    var x = Math.ceil(Math.random()*31);
                    e.preventDefault();
                    let html = `
                        <tr data-id="${x}" id="row-${x}" class="strg">
                            <td class="wc-storage-method">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <div class="input-group-area">
                                                <input type="text" placeholder="Numero de tarjeta" id="ccnum${x}" maxlength="16" style="border-radius: 0;" class="ccnum">
                                            </div>
                                            <div id="type" class="input-group-icon"></div>
                                        </div>
                                        <div class="message"></div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group">
                                            <div class="input-group-area">
                                                <input  style="border-radius:0; border:transparent; border-bottom:1px solid #f2f2f2;" type="text" placeholder="CVV" maxlength="3" id="cvv${x}" class="cvv">
                                            </div>
                                            <div class="input-group-icon validator"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="wc-storage-expire">
                                <div class="row">
                                    <div class="col-12">
                                        <span id="expiration">
                                            <select id="expMonth${x}" name="expMonth" class="select-expiration" style="border-radius:0;">
                                            <option value="01" selected>01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            </select>
                                            <select id="expYear${x}" name="expYear" class="select-expiration" style="border-radius:0;">
                                            </select>
                                        </span
                                    </div>
                                </div>
                            </td>
                            <td class="wc-storage-default">
                                <div class="row">
                                    <button data-save="${x}" class="button button-primary wc-payment-method-class-add" style="margin-right:5px;">Guardar</button>
                                    <button data-delete="${x}" class="button button-danger wc-payment-method-class-delete">Borrar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    jQuery('body').on('click', '.wc-payment-method-class-delete', function(e){
                        e.preventDefault()
                        const row = jQuery(this).data('delete')
                        jQuery("#row-"+row).remove()
                    })
                    jQuery('#fields_wrapper').append(html)
                    var yearsToShow = 10;
                    var thisYear = (new Date()).getFullYear();
                    for (var y = thisYear; y < thisYear + yearsToShow; y++) {
                    var yearOption = document.createElement("option");
                    yearOption.value = y;
                    yearOption.text = y;
                    document.getElementById(`expYear${x}`).appendChild(yearOption);
                    }
                })
                jQuery("body").on("keyup", ".ccnum", function(e){
                    jQuery("#type").empty()
                    jQuery(".message").empty()
                    const imgVisa = '<img style="width:30px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/visa.svg">' 
                    const imgMaster = '<img style="width:30px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/master.svg">' 
                    const imgError = '<img style="width:10px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/equis.png">' 
                    let cardAccepted = false;
                    const num = jQuery(this).val().toString();
                    let charCount = num.length;
        
                    /* VALIDACION DE TIPO */
                    const master = num[0] + num[1]
                    if (charCount > 0) {
                        if(num[0] == "4") {
                            jQuery("#type").html(imgVisa);
                            cardAccepted = true;
                        }else if(master == "51" || master == "55" || master == "53") {
                            cardAccepted = true;
                            jQuery("#type").html(imgMaster);
                        }else if(num[0] != "4" || master != "51" || master != "55" || master != "53"){
                            cardAccepted = false;
                        }
                        
                        if(charCount == 13 || charCount == 14 || charCount == 15 || charCount == 16){
                            const valid = isValid(num, charCount);
                            if (!cardAccepted && valid) {
                                jQuery("#type").html(imgError);
                                jQuery(".message").html("<p>Solo tarjetas de tipo Visa o Mastercard</p>");
                                isValidInfo = false;
                            }else if(cardAccepted && !valid){
                                jQuery("#type").html(imgError);
                                jQuery(".message").html("<p>Numero de tarjeta incorrecta.</p>");
                                isValidInfo = false;
                            }else if (!cardAccepted && !valid) {
                                jQuery("#type").html(imgError);
                                jQuery(".message").html("<p>Numero de tarjeta incorrecta.</p>");
                                isValidInfo = false;
                            }else if (cardAccepted && valid) {
                                isValidInfo = true;
                            }
                        }else{
                            isValidInfo = false;
                        }    
                    }else{
                        jQuery('#type').empty()
                    }
                });

                jQuery("body").on("keyup", ".cvv", function(e){
                    const imgError = '<img style="width:10px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/equis.png">' 
                    const num = jQuery(this).val().toString();
                    let charCount = num.length;
                    if (charCount != '3' || isNaN(num)) {
                        jQuery(".validator").html(imgError);
                        isValidCVV = false;
                    }else{
                        jQuery(".validator").empty();
                        isValidCVV = true;
                    }
                })
               
                jQuery("body").on("click", '.wc-payment-method-class-add', function(e){
                    e.preventDefault()
                    const row = jQuery(this).data('save')
                    if (!isValidInfo) {
                        jQuery("#ccnum"+row).focus()        
                    }else if (!isValidCVV) {
                        jQuery("#cvv"+row).focus()        
                    }else{
                        const data = {
                            ccnum : jQuery("#ccnum"+row).val(),
                            cvv   : jQuery("#cvv"+row).val(),
                            exp   : jQuery("#expMonth"+row).val() + "/" + jQuery("#expYear"+row).val(),
                        };
                        console.log(data)
                        jQuery.ajax({
                            type: "POST",
					        url: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php',
                            data: {
                                action   : 'redypagos_update_card_info',
                                nonce    : '<?php wp_create_nonce(); ?>',
                                data     :  btoa(JSON.stringify(data))
                            },
                            beforeSend: function(){  
                                jQuery("#fields_wrapper").LoadingOverlay("show", {
                                    size: 5
                                });      
                            },
                            success: function(data) {
						        jQuery(".notification").empty().append(`<div class="woocommerce-message" role="alert">${data}</div>`)
                                jQuery("#fields_wrapper").LoadingOverlay("hide");
                                location.reload()
                            },                
                            error: function(err){
						        jQuery(".notification").empty().append(`<div id="message" class="woocommerce-Message woocommerce-Message--info woocommerce-info"><p><strong>'${err.responseJSON}'</strong></p></div>`)
                                jQuery("#fields_wrapper").LoadingOverlay("hide");
                            }
                        });
                    }
                })
                jQuery("body").on("click", '#delete-card', function(e){
                    e.preventDefault()
                    jQuery.ajax({
                        type: "POST",
                        url: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php',
                        data: {
                            action   : 'redypagos_delete_card_info',
                            nonce    : '<?php wp_create_nonce(); ?>'
                        },
                        beforeSend: function(){  
                            jQuery("#fields_wrapper").LoadingOverlay("show", {
                                size: 5
                            });      
                        },
                        success: function(data) {
                            jQuery("#fields_wrapper").LoadingOverlay("hide");
                            location.reload()
                        },                
                        error: function(err){
                            jQuery(".notification").empty().append(`<div id="message" class="woocommerce-Message woocommerce-Message--info woocommerce-info"><p><strong>'${err.responseJSON}'</strong></p></div>`)
                            jQuery("#fields_wrapper").LoadingOverlay("hide");
                        }
                    });
                })
            });
		}
	)
    
    function isValid(ccNum, charCount){
        let double = true;
        let numArr = [];
        let sumTotal = 0;
        for(i=0; i<charCount; i++){
            let digit = parseInt(ccNum.charAt(i));
            if(double){
                digit = digit * 2;
                digit = toSingle(digit);
                double = false;
            } else {
                double = true;
            }
            numArr.push(digit);
        }
        
        for(i=0; i<numArr.length; i++){
            sumTotal += numArr[i];
        }
        const diff = eval(sumTotal % 10);
        return (diff == "0");
    }

    function toSingle(digit){
        if(digit > 9){
            const tmp = digit.toString();
            const d1 = parseInt(tmp.charAt(0));
            const d2 = parseInt(tmp.charAt(1));
            return (d1 + d2); 
        } else {
            return digit;
        }
    }
</script>
<table class="wc-payment-method-classes widefat">
    <thead>
        <tr>
            <th class="wc-payment-method-class-method">Datos de tarjeta</th>
            <th class="wc-payment-method-class-expire">Expira</th>
            <th class="wc-payment-method-class-default"></th>
        </tr>
    </thead>
    <?php 
        $data = get_user_meta( wp_get_current_user()->ID, 'rc_card_details', true);
        if (empty($data)) {
            ?>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <button id="add_payment_method" style="margin: top 10px;" class="button button-primary wc-payment-method-class-save" value="Guardar Cambios">Agregar metodo de pago</button>
                        </td>
                    </tr>
                </tfoot>
            <?php
        }
    ?>
    <tbody class="wc-payment-method-class-rows" style="position: relative; zoom: 1;" id="fields_wrapper">
        <?php 
            $data = get_user_meta( wp_get_current_user()->ID, 'rc_card_details', true);
            if ( !empty($data) ) {
                $data = json_decode( base64_decode( $data ), true );
                if ($data['brand'] === "VISA") {
                    $img = '<img style="width:30px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/visa.svg">'; 
                }else{
                    $img = '<img style="width:30px;max-width: initial;" src="/wp-content/plugins/wc-redypagos-gateway/assets/img/master.svg">';
                }
                ?>
                    <tr data-id="<?php echo $data['last']; ?>" class="strg">
                        <td class="wc-redypagos-method">
                            <div class="row">
                                <div class="col-3">
                                    <?php printf('%s', $img); ?>
                                </div>
                                <div class="col-9">
                                    <?php printf('%s', '****'.$data['last']); ?>
                                </div>
                            </div>
                        </td>
                        <td class="wc-redypagos-expire">
                            <div class="view"><?php printf('%s', $data['exp']); ?></div>
                        </td>
                        <td class="wc-redypagos-default" style="text-align:center;">
                            <div class="view"><button id="delete-card" class="button button-danger wc-payment-method-class-delete">Borrar</button></div>
                        </td>
                    </tr>
                <?php
            }else{
                ?>
                    <tr id="info">
                        <td class="wc-redypagos-blank" colspan="9">
                            <p>No has agregado ninguna tarjeta.</p>
                        </td>
                    </tr>
                <?php
            }
        ?>
    </tbody>
</table>
