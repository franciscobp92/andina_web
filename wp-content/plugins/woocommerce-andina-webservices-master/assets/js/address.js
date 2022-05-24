jQuery(function($){ 
	$(document).ready(function(){
		if (params.isLoggedIn) {
			listAddress()
		}else{
			$('[name="address_user"]').hide()	
		}

		$("a.btn.btn-style-link.btn-color-primary.create-account-button").click(function(event){
			event.preventDefault()
			window.open('https://andinalicores.com.ec/b2b/', '_blank');
		})

		$("a.btn.woodmart-to-register").click(function(event){
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
			    width: "38px",
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
                    action   : 'al_webservice_change_user_address',
                    nonce    :  params.nonce,
                    data     :  $('[name="address_user"]').val()
                },
                beforeSend: function(){        
                    $.LoadingOverlay("show", {
					    size: 5
					});
                },
                success: function(data) {
					if (!data.error) {
						$.LoadingOverlay("hide");
						location.reload();
					}
                },                
                error: function(response){
					$.LoadingOverlay("hide");
					location.reload();
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
			if (b.codigo_dir == params.code_dir_actual) {
				options += '<option selected value="' + b.cld_direccion + '">' + b.cld_direccion + '</option>';	
			}else{
				options += '<option value="' + b.cld_direccion + '">' + b.cld_direccion + '</option>';
			}
			
		})
		$('[name="address_user"]').html(options);
	}
})