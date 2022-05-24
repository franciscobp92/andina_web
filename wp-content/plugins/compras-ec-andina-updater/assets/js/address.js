jQuery(function($){ 
	$(document).ready(function(){
		if (params.isLoggedIn) {
			listAddress()
		}else{
			$('[name="address_user"]').hide()	
		}
		$("a.btn.btn-style-link.btn-color-primary.create-account-button").click(function(){
			event.preventDefault()
			window.open('https://andinalicores.com.ec/b2b/', '_blank');
		})
		$("a.btn.woodmart-to-register").click(function(){
			event.preventDefault()
			window.open('https://andinalicores.com.ec/b2b/', '_blank');
		})
		$( "#presupuesto" ).hover(
		  function() {
		    $("#icon-arrow").hide();
			$("#credito").show();
			$("#sdisponible").show();
			$("#presupuesto").css({background:'#fff'});
			$( "#presupuesto" ).animate({
			    width: "250px",
			    background: "#fff",
			  }, 400 );
		  }, function() {
		    $("#presupuesto").css({background:'#092143'});
			$( "#presupuesto" ).animate({
			    width: "50px",
			    background: "#092143",
			  }, 300 );
			$("#credito").hide();
			$("#sdisponible").hide();
			$("#icon-arrow").show();
		  }
		);
		$('[name="address_user"]').change(function(){
			jQuery.ajax({
                type: "POST",
                url: params.url,
                data: {
                    action   : 'change_user_address',
                    nonce    :  params.nonce,
                    data     :  $('[name="address_user"]').val()
                },
                beforeSend: function(){        
                    $.LoadingOverlay("show", {
					    size: 5
					});

                },
                success: function(data) {
                    $.LoadingOverlay("hide");
                    location.reload();
                },                
                error: function(response){
                }
            });
		})	
	})

	function listAddress(){	
		$('[name="address_user"]').show();
		$("#placeDelivery").show();
		$("#placeDeliveryMovil").show();
		let data = params.array
		let options = '';
		$.each(data, function(a,b){
			if (b.dir == params.actual[0]) {
				options += '<option selected value="' + b.bodega + '">' + b.direccion + '</option>';	
			}else{
				options += '<option value="' + b.bodega + '">' + b.direccion + '</option>';
			}
			
		})
		$('[name="address_user"]').html(options);
	}
})