!function(o){woodmartThemeModule.$document.on("wdPjaxStart",function(){woodmartThemeModule.hideShopSidebar()}),woodmartThemeModule.$document.on("wdShopPageInit",function(){woodmartThemeModule.hiddenSidebar()}),woodmartThemeModule.hiddenSidebar=function(){var e=woodmartThemeModule.$body.hasClass("rtl")?"right":"left",d=((woodmartThemeModule.$body.hasClass("offcanvas-sidebar-desktop")&&1024<woodmartThemeModule.windowWidth||woodmartThemeModule.$body.hasClass("offcanvas-sidebar-tablet")&&woodmartThemeModule.windowWidth<=1024)&&(o(".area-sidebar-shop").addClass("wd-side-hidden wd-"+e+" wd-inited wd-scroll"),o(".area-sidebar-shop .widget-area").addClass("wd-scroll-content")),woodmartThemeModule.$body.hasClass("offcanvas-sidebar-mobile")&&woodmartThemeModule.windowWidth<=768&&(o(".sidebar-container").addClass("wd-side-hidden wd-"+e+" wd-inited wd-scroll"),o(".sidebar-container .widget-area").addClass("wd-scroll-content")),woodmartThemeModule.$body.off("click",".wd-show-sidebar-btn, .wd-sidebar-opener").on("click",".wd-show-sidebar-btn, .wd-sidebar-opener",function(e){e.preventDefault(),o(".sidebar-container").hasClass("wd-opened")?woodmartThemeModule.hideShopSidebar():d()}),woodmartThemeModule.$body.on("click touchstart",".wd-close-side, .close-side-widget",function(){woodmartThemeModule.hideShopSidebar()}),function(){o(".sidebar-container").addClass("wd-opened"),o(".wd-close-side").addClass("wd-close-side-opened")});woodmartThemeModule.$document.trigger("wdHiddenSidebarsInited")},woodmartThemeModule.hideShopSidebar=function(){o(".sidebar-container").removeClass("wd-opened"),o(".wd-close-side").removeClass("wd-close-side-opened")},o(document).ready(function(){woodmartThemeModule.hiddenSidebar()})}(jQuery);