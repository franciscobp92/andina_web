(window.webpackJsonp=window.webpackJsonp||[]).push([[42],{760:function(t,e,r){"use strict";var n=r(219),a=r(0),c=r(2);e.a=function(t,e,r){var o=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"",u=bwfcrm_contacts_data&&bwfcrm_contacts_data.header_data?bwfcrm_contacts_data.header_data:{},i=bwfcrm_contacts_data&&bwfcrm_contacts_data.contacts_count?parseInt(bwfcrm_contacts_data.contacts_count):0,s=Object(n.a)(),f=s.setActiveMultiple,l=s.resetHeaderMenu,b=s.setL2NavType,p=s.setL2Nav,m=s.setBackLink,v=s.setL2Title,d=s.setL2Content,O=s.setBackLinkLabel,y=s.setPageHeader;return Object(a.useEffect)((function(){l(),!e&&i>0&&b("menu"),!e&&i>0&&p(u.contacts_nav),f({leftNav:"contacts",rightNav:t}),e&&m(e),e&&O("All Contacts"),r&&v(r),r&&"Export"===r&&(o&&d(o),b("menu"),p({export:{name:Object(c.__)("All","wp-marketing-automations"),link:"admin.php?page=autonami&path=/export"}})),!e&&i>0&&o&&d(o),y("Contacts")}),[t,r]),!0}},773:function(t,e,r){"use strict";var n=r(0),a=r(13),c=r.n(a),o=r(7),u=r(783),i=r(780);r(779);function s(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function f(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?s(Object(r),!0).forEach((function(e){l(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):s(Object(r)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function l(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}function b(t,e,r,n,a,c,o){try{var u=t[c](o),i=u.value}catch(t){return void r(t)}u.done?e(i):Promise.resolve(i).then(n,a)}function p(t){return function(){var e=this,r=arguments;return new Promise((function(n,a){var c=t.apply(e,r);function o(t){b(c,n,a,o,u,"next",t)}function u(t){b(c,n,a,o,u,"throw",t)}o(void 0)}))}}function m(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(t)))return;var r=[],n=!0,a=!1,c=void 0;try{for(var o,u=t[Symbol.iterator]();!(n=(o=u.next()).done)&&(r.push(o.value),!e||r.length!==e);n=!0);}catch(t){a=!0,c=t}finally{try{n||null==u.return||u.return()}finally{if(a)throw c}}return r}(t,e)||function(t,e){if(!t)return;if("string"==typeof t)return v(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);"Object"===r&&t.constructor&&(r=t.constructor.name);if("Map"===r||"Set"===r)return Array.from(t);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return v(t,e)}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function v(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}e.a=function(){var t=m(Object(n.useState)(!1),2),e=t[0],r=t[1],a=m(Object(n.useState)({}),2),s=a[0],l=a[1],b=Object(n.useCallback)(p(regeneratorRuntime.mark((function t(){var e;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return r(!0),t.prev=1,t.next=4,c()({method:"GET",path:Object(o.g)("/fields")});case 4:(e=t.sent)&&e.result&&l(e.result),t.next=11;break;case 8:t.prev=8,t.t0=t.catch(1),console.log(t.t0);case 11:r(!1);case 12:case"end":return t.stop()}}),t,null,[[1,8]])}))));Object(n.useEffect)((function(){Object(o.X)()&&b()}),[]);var v={};if(s)for(var d in s){var O=s[d],y=Object(u.b)(O.type),j=O.meta.options,w=O.name;v["bwf_cf".concat(O.ID)]=Object(u.a)(w,"Custom Field","contact_custom_fields",12,y,j)}var h=f(f({},i.a),v);return!!e||h}},779:function(t,e,r){},847:function(t,e,r){"use strict";r.r(e);var n=r(0),a=r(791),c=r(79),o=r(7);e.default=function(){return Object(o.d)("Audiences"),Object(n.createElement)(n.Fragment,null,Object(n.createElement)(c.a,null),Object(n.createElement)(a.b,null))}}}]);