/**handles:woocommerce_scripts**/
jQuery(function($){
    
    function actualizaCarrito(){
        var t,e=document.getElementsByClassName("qty");
        for(t=0;t<e.length;t++)if(!e[t].value)return;
            jQuery('[name="update_cart"]').trigger("click")
    }

    function updateCart(){
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'updated_checkout'
            },
            beforeSend: function(){
                $.LoadingOverlay("show", {
                    image       : "",
                    text        : "Actualizando Carrito..."
                });
            },
            success: function(data) {
                let totalDiscount = 0
                let subtotal = 0
                let maquetartotal  = ''
                let subHtml = ''
                $.each(data, function(a,b){
                    totalDiscount = parseFloat(b.totalDescuento) + parseFloat(totalDiscount);
                    subtotal      += parseFloat(b.subtotal) 
                    let html = ''
                    html += `
                        <strong>
                            <span class="woocommerce-Price-amount amount">
                                <bdi>
                                    ${b.descuento}
                                    <span class="woocommerce-Price-currencySimbol">%</span>
                                </bdi>
                            </span>
                        </strong>
                `;

                $(".product-discount#"+b.product_id).empty().append(html)
                $("#new"+b.product_id).empty().append(
                    `
                    <span class="woocommerce-Price-amount amount">
                        <span class="woocommerce-Price-currencySymbol">$</span>
                            ${parseFloat(b.precioNuevo).toFixed(2)}
                    </span>`
                )
                $("#subtotal"+b.product_id).empty().append(
                    `
                    <span class="woocommerce-Price-amount amount">
                        <span class="woocommerce-Price-currencySymbol">$</span>
                            ${parseFloat(b.subtotal).toFixed(2)}
                    </span>`
                )
                })

                maquetartotal += `
                    <tr class="discount">
                        <th>Total Descuento</th>
                        <td>
                            <span class="woocommerce-Price-amount amount">
                                <span class="woocommerce-Price-currencySymbol">$</span>
                                ${parseFloat(totalDiscount).toFixed(2)}
                            </span>
                        </td>    
                    </tr>
                `;
                 subHtml += `
                    <span class="woocommerce-Price-amount amount">
                        <span class="woocommerce-Price-currencySymbol">$</span>
                        ${parseFloat(subtotal).toFixed(2)}
                    </span>
                `;
                $(".cart-totals-section .cart-subtotal").before(maquetartotal)
                $(".cart-subtotal td").empty().append(subHtml)
                $.LoadingOverlay("hide");

            },                
            error: function(response){
                $.LoadingOverlay("hide");
           }
           
        });
    }
    
    function updateCheckout(){
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'updated_checkout'
            },
            beforeSend: function(){
                $("#order_review").LoadingOverlay("show", {
                    size       : 5,
                });
            },
            success: function(data) {
                let totalDiscount = 0;
                let subtotal = 0
                let html = ''
                let subHtml = ''
                $.each(data, function(a,b){
                    totalDiscount = parseFloat(b.totalDescuento) + parseFloat(totalDiscount);
                    subtotal      += parseFloat(b.subtotal) 
                });

                
                html += `
                    <tr class="discount">
                        <th>Total Descuento</th>
                        <td>
                            <span class="woocommerce-Price-amount amount">
                                <span class="woocommerce-Price-currencySymbol">$</span>
                                ${parseFloat(totalDiscount).toFixed(2)}
                            </span>
                        </td>    
                    </tr>
                `;
                subHtml += `
                            <span class="woocommerce-Price-amount amount">
                                <span class="woocommerce-Price-currencySymbol">$</span>
                                ${parseFloat(subtotal).toFixed(2)}
                            </span>
                `;
                $(".cart-subtotal").before(html)
                $(".cart-subtotal td").empty().append(subHtml)
                $("#order_review").LoadingOverlay("hide");
            },                
            error: function(response){
                $("tbody").LoadingOverlay("hide");
           }
           
        });
    }
    

    $(document).ready(function(){
        $(".site-content").removeClass('col-lg-9')
        $(".site-content").addClass('col-lg-12')
        $(".sidebar-container").hide()
        
        $(".checkout-button.button.alt.wc-forward").on("click", function(){
            var href = $(this).attr("href");
            event.preventDefault()
            actualizaCarrito()
            setTimeout(function(){
                window.location = href;        
            },2500)
            
            
        })

        $( document.body ).trigger( 'updated_cart_totals' );

        jQuery('body').on('updated_cart_totals', function(){
            updateCart()
        });

        $( document.body ).trigger( 'updated_checkout' );
    
        jQuery('body').on('updated_checkout', function(){
            updateCheckout()
        });

        $('form.woocommerce-checkout').on( 'change', '#billing_address_1', function(){
            $('body').trigger('update_checkout');
        })
   
    });
});