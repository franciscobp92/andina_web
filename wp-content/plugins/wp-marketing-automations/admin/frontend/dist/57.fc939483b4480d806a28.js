(window.webpackJsonp=window.webpackJsonp||[]).push([[57],{777:function(t,e,a){"use strict";var n=a(219),o=a(0),r=a(2),i=a(7),c=function(t){var e={workflow:{name:Object(r.__)("Workflow","wp-marketing-automations"),link:"admin.php?page=autonami-automations&edit=".concat(t)},engagement:{name:Object(r.__)("Engagement","wp-marketing-automations"),link:"admin.php?page=autonami&path=/automation/".concat(t,"/engagement")}};return Object(i.fb)()&&(e.orders={name:Object(r.__)("Orders","wp-marketing-automations"),link:"admin.php?page=autonami&path=/automation/".concat(t,"/orders")}),e};e.a=function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"",a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",i=arguments.length>3&&void 0!==arguments[3]&&arguments[3],u=arguments.length>4&&void 0!==arguments[4]?arguments[4]:"",s=arguments.length>5&&void 0!==arguments[5]?arguments[5]:0,m=arguments.length>6&&void 0!==arguments[6]&&arguments[6],l=bwfcrm_contacts_data&&bwfcrm_contacts_data.header_data?bwfcrm_contacts_data.header_data:{},p=l.automation_nav,f=Object(n.a)(),d=f.setActiveMultiple,b=f.resetHeaderMenu,h=f.setL2NavType,v=f.setL2Nav,g=f.setBackLink,w=f.setL2Title,O=f.setL2Content,j=f.setBackLinkLabel,_=f.setL2NavAlign,y=f.setPageHeader;return Object(o.useEffect)((function(){b(),!i&&h("menu"),v(s?c(s):p),d({leftNav:"automations",rightNav:t}),a&&g(a),a&&s&&g("admin.php?page=autonami-automations&edit=".concat(s)),i&&j(Object(r.__)("All Automations","wp-marketing-automations")),s&&j(Object(r.__)("Back to Automation","wp-marketing-automations")),s&&_("right"),e&&w(e),!a&&u&&O(u),y("Automations"),m&&v({})}),[t]),!0}},892:function(t,e,a){"use strict";a.r(e);var n=a(0),o=a(777),r=a(79),i=a(2),c=a(246),u=a(13),s=a.n(u),m=a(7),l=a(121),p=a(14);function f(t,e,a,n,o,r,i){try{var c=t[r](i),u=c.value}catch(t){return void a(t)}c.done?e(u):Promise.resolve(u).then(n,o)}function d(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(t)))return;var a=[],n=!0,o=!1,r=void 0;try{for(var i,c=t[Symbol.iterator]();!(n=(i=c.next()).done)&&(a.push(i.value),!e||a.length!==e);n=!0);}catch(t){o=!0,r=t}finally{try{n||null==c.return||c.return()}finally{if(o)throw r}}return a}(t,e)||function(t,e){if(!t)return;if("string"==typeof t)return b(t,e);var a=Object.prototype.toString.call(t).slice(8,-1);"Object"===a&&t.constructor&&(a=t.constructor.name);if("Map"===a||"Set"===a)return Array.from(t);if("Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a))return b(t,e)}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function b(t,e){(null==e||e>t.length)&&(e=t.length);for(var a=0,n=new Array(e);a<e;a++)n[a]=t[a];return n}e.default=function(){var t=Object(p.h)();Object(m.d)("Import Automations"),Object(o.a)("automations","Import","admin.php?page=autonami&path=/automations",!0,"",0,!0);var e=d(Object(n.useState)({status:!1,loading:!0}),2),a=e[0],u=e[1],b=d(Object(n.useState)(null),2),h=b[0],v=b[1],g=Object(n.createElement)("div",{className:"bwf-p-15 bwf_display_block"},Object(n.createElement)("div",{className:"bwf_clear_15"}),Object(n.createElement)("div",{className:""},"Import Automations from a JSON file"),Object(n.createElement)("div",{className:"bwf_clear_20"}),Object(n.createElement)("div",{className:"bwf-p bwf-p2 bwf-pt-15"},"This tool allows you to import the automations from the JSON file.")),w=function(){var e,a=(e=regeneratorRuntime.mark((function e(){var a;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return u({status:!0,loading:!0}),e.prev=1,(a=new FormData).append("files",h),e.next=6,s()({method:"POST",body:a,path:Object(m.g)("/automations/import")}).then((function(e){if(200==e.code){if(e.result.hasOwnProperty("automation_id"))return void(window.location.href="admin.php?page=autonami-automations&edit="+e.result.automation_id);u({status:!0,success:Object(i.__)("Automation successfully imported.","wp-marketing-automations")}),setTimeout((function(){Object(p.j)({path:"/automations/"},"/",t)}),1e3)}else v(null),u({status:!0,error:Object(i.__)("Unable to import automation.","wp-marketing-automations")})}));case 6:e.next=12;break;case 8:e.prev=8,e.t0=e.catch(1),v(null),u({status:!0,error:Object(i.__)("Unable to import automation.","wp-marketing-automations")});case 12:case"end":return e.stop()}}),e,null,[[1,8]])})),function(){var t=this,a=arguments;return new Promise((function(n,o){var r=e.apply(t,a);function i(t){f(r,n,o,i,c,"next",t)}function c(t){f(r,n,o,i,c,"throw",t)}i(void 0)}))});return function(){return a.apply(this,arguments)}}();return Object(n.useEffect)((function(){h&&w()}),[h]),Object(n.createElement)(n.Fragment,null,Object(n.createElement)(r.a,null),Object(n.createElement)("div",{className:"bwfcrm-overview-wrap"},Object(n.createElement)(c.a,{onFileSelected:function(t){return v(t)},htmlData:g,filetype:"application/json",btnLabel:Object(i.__)("Drop your JSON file here OR","wp-marketing-automations")})),Object(n.createElement)(l.a,{isLoading:a.loading,successMessage:a.success,errorMessage:a.error,isOpen:a.status,onRequestClose:function(){return u({status:!1})}}))}}}]);