!function(l){l.each(["frontend/element_ready/wd_accordion.default","frontend/element_ready/wd_single_product_tabs.default","frontend/element_ready/wd_single_product_reviews.default"],function(e,o){woodmartThemeModule.wdElementorAddAction(o,function(e){woodmartThemeModule.accordion(),l(".wc-tabs-wrapper, .woocommerce-tabs").trigger("init"),e.find("#rating").parent().find("> .stars").remove(),e.find("#rating").trigger("init")})}),woodmartThemeModule.accordion=function(){var e=window.location.hash,o=window.location.href;l(".woocommerce-review-link").on("click",function(){l(".tabs-layout-accordion .wd-accordion-title.tab-title-reviews:not(.active)").click()}),l(".wd-accordion").each(function(){var a,n,t=l(this),r=t.find("> .wd-accordion-item > .wd-accordion-title"),i=t.find("> .wd-accordion-item > .wd-accordion-content"),c="wd-active",d=t.data("state"),s=300;t.hasClass("wd-inited")||(a=function(e){var o=r.filter('[data-accordion-index="'+e+'"]'),e=i.filter('[data-accordion-index="'+e+'"]');o.addClass(c),e.stop(!0,!0).slideDown(s).addClass(c),"first"!==d||t.hasClass("wd-inited")||e.stop(!0,!0).show().css("display","block"),t.addClass("wd-inited"),woodmartThemeModule.$document.trigger("resize.vcRowBehaviour"),woodmartThemeModule.$document.trigger("wood-images-loaded")},n=function(){var e=r.filter("."+c),o=i.filter("."+c);e.removeClass(c),o.stop(!0,!0).slideUp(s).removeClass(c)},"first"===d&&a(r.first().data("accordion-index")),r.off("click").on("click",function(){var o=l(this),e=l(this).data("accordion-index"),t=r.filter('[data-accordion-index="'+e+'"]').hasClass(c),i=o.parent().index(),d=o.parent().siblings().find(".wd-active").parent(".wd-tab-wrapper").index();!o.hasClass("wd-active")&&-1!==i||(d=i),t?n():(n(),a(e)),o.parents(".tabs-layout-accordion")&&setTimeout(function(){var e;woodmartThemeModule.$window.width()<1024&&d<i&&(e=0<(e=l(".sticky-header")).length?e.outerHeight():0,l("html, body").animate({scrollTop:o.offset().top-o.outerHeight()-e-50},500))},s)}),(0<=e.toLowerCase().indexOf("comment-")||"#reviews"===e||"#tab-reviews"===e||0<o.indexOf("comment-page-")||0<o.indexOf("cpage="))&&t.find(".tab-title-reviews").trigger("click"))})},l(document).ready(function(){woodmartThemeModule.accordion()})}(jQuery);