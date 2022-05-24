jQuery(function($){
    function actualizaCarrito(){
        var t,e=document.getElementsByClassName("qty");
        for(t=0; t < e.length; t++) if( !e[t].value ) return;
            jQuery('[name="update_cart"]').trigger("click")
    }
    
    $(document).ready(function(){
        $(".site-content").removeClass('col-lg-9')
        $(".site-content").addClass('col-lg-12')
        $(".sidebar-container").hide()
        
        $(".checkout-button.button.alt.wc-forward").on("click", function(event){
            var href = $(this).attr("href");
            event.preventDefault()
            actualizaCarrito()
            setTimeout(function(){
                window.location = href;        
            },2500)
        })

        $('form.woocommerce-checkout').on( 'change', '#billing_address_1', function(){
            $('body').trigger('update_checkout');
        })
    });
});