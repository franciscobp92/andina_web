!function(r){woodmartThemeModule.$document.on("wdShopPageInit wdLoadMoreLoadProducts wdArrowsLoadProducts wdProductsTabsLoaded wdUpdateWishlist",function(){woodmartThemeModule.productMoreDescription()}),r.each(["frontend/element_ready/wd_products.default","frontend/element_ready/wd_products_tabs.default"],function(e,o){woodmartThemeModule.wdElementorAddAction(o,function(){woodmartThemeModule.productMoreDescription()})}),woodmartThemeModule.productMoreDescription=function(){r(".wd-hover-base").on("mouseenter touchstart",function(){var e,o=r(this).find(".wd-more-desc"),d=o.find(".wd-more-desc-inner"),t=o.find(".wd-more-desc-btn");o.hasClass("wd-more-desc-calculated")||(e=o.outerHeight(),30<(d=d.outerHeight()-e)?t.addClass("wd-shown"):0<d&&o.css("height",e+d),o.addClass("wd-more-desc-calculated"))}),woodmartThemeModule.$body.on("click",".wd-more-desc-btn",function(e){e.preventDefault();e=r(this);e.parent().addClass("wd-more-desc-full"),woodmartThemeModule.$document.trigger("wdProductMoreDescriptionOpen",[e.parents(".wd-hover-base")])})},r(document).ready(function(){woodmartThemeModule.productMoreDescription()})}(jQuery);