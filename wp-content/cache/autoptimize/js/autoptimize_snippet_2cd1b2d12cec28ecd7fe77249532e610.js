jQuery((function($){"use strict";$(".pwb-dropdown-widget").on("change",(function(){var href=$(this).find(":selected").val();location.href=href})),"function"==typeof $.fn.slick&&($(".pwb-carousel").slick({slide:".pwb-slick-slide",infinite:!0,draggable:!1,prevArrow:'<div class="slick-prev"><span>'+pwb_ajax_object.carousel_prev+"</span></div>",nextArrow:'<div class="slick-next"><span>'+pwb_ajax_object.carousel_next+"</span></div>",speed:300,lazyLoad:"progressive",responsive:[{breakpoint:1024,settings:{slidesToShow:4,draggable:!0,arrows:!1}},{breakpoint:600,settings:{slidesToShow:3,draggable:!0,arrows:!1}},{breakpoint:480,settings:{slidesToShow:2,draggable:!0,arrows:!1}}]}),$(".pwb-product-carousel").slick({slide:".pwb-slick-slide",infinite:!0,draggable:!1,prevArrow:'<div class="slick-prev"><span>'+pwb_ajax_object.carousel_prev+"</span></div>",nextArrow:'<div class="slick-next"><span>'+pwb_ajax_object.carousel_next+"</span></div>",speed:300,lazyLoad:"progressive",responsive:[{breakpoint:1024,settings:{slidesToShow:3,draggable:!0,arrows:!1}},{breakpoint:600,settings:{slidesToShow:2,draggable:!0,arrows:!1}},{breakpoint:480,settings:{slidesToShow:1,draggable:!0,arrows:!1}}]}));var PWBFilterByBrand=function(){var baseUrl=[location.protocol,"//",location.host,location.pathname].join(""),currentUrl=window.location.href,marcas=[];$('.pwb-filter-products input[type="checkbox"]').each((function(index){$(this).prop("checked")&&marcas.push($(this).val())})),currentUrl=(marcas=marcas.join())?-1===(currentUrl=(currentUrl=currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i,"")).replace(/\/page\/\d*\//i,"")).indexOf("?")?currentUrl+"?pwb-brand-filter="+marcas:currentUrl+"&pwb-brand-filter="+marcas:baseUrl,location.href=currentUrl},PWBRemoveFilterByBrand=function(){var baseUrl=[location.protocol,"//",location.host,location.pathname].join(""),currentUrl=window.location.href;currentUrl=(currentUrl=currentUrl.replace(/&?pwb-brand-filter=([^&]$|[^&]*)/i,"")).replace(/\/page\/\d*\//i,""),location.href=currentUrl};$(".pwb-apply-filter").on("click",(function(){PWBFilterByBrand()})),$(".pwb-remove-filter").on("click",(function(){PWBRemoveFilterByBrand()})),$(".pwb-filter-products.pwb-hide-submit-btn input").on("change",(function(){PWBFilterByBrand()}));var brands=PWBgetUrlParameter("pwb-brand-filter");if(null!=brands){var brands_array=brands.split(",");$('.pwb-filter-products input[type="checkbox"]').prop("checked",!1);for(var i=0,l=brands_array.length;i<l;i++)$('.pwb-filter-products input[type="checkbox"]').each((function(index){$(this).val()&&brands_array[i]==$(this).val()&&$(this).prop("checked",!0)}))}else $('.pwb-filter-products input[type="checkbox"]').prop("checked",!1)}));var PWBgetUrlParameter=function PWBgetUrlParameter(sParam){var sPageURL,sURLVariables=decodeURIComponent(window.location.search.substring(1)).split("&"),sParameterName,i;for(i=0;i<sURLVariables.length;i++)if((sParameterName=sURLVariables[i].split("="))[0]===sParam)return void 0===sParameterName[1]||sParameterName[1]};