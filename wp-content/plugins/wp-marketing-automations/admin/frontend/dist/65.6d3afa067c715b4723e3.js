(window.webpackJsonp=window.webpackJsonp||[]).push([[65],{868:function(t,e,r){"use strict";r.r(e);var n=r(0),a=r(233),o=r(13),c=r.n(o),i=r(7),u=r(26),s=r(67),l=r(2),p=r(759);function f(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function b(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}function m(t,e,r,n,a,o,c){try{var i=t[o](c),u=i.value}catch(t){return void r(t)}i.done?e(u):Promise.resolve(u).then(n,a)}function y(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(t)))return;var r=[],n=!0,a=!1,o=void 0;try{for(var c,i=t[Symbol.iterator]();!(n=(c=i.next()).done)&&(r.push(c.value),!e||r.length!==e);n=!0);}catch(t){a=!0,o=t}finally{try{n||null==i.return||i.return()}finally{if(a)throw o}}return r}(t,e)||function(t,e){if(!t)return;if("string"==typeof t)return d(t,e);var r=Object.prototype.toString.call(t).slice(8,-1);"Object"===r&&t.constructor&&(r=t.constructor.name);if("Map"===r||"Set"===r)return Array.from(t);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return d(t,e)}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function d(t,e){(null==e||e>t.length)&&(e=t.length);for(var r=0,n=new Array(e);r<e;r++)n[r]=t[r];return n}e.default=function(t){var e=t.query,r=t.campaignId,o=e.chartType?e.chartType:"line",d=p.a.getCampaignData(),v=d&&d.type?parseInt(d.type):1,O=y(Object(n.useState)([]),2),h=O[0],j=O[1],w=y(Object(n.useState)(!1),2),g=w[0],k=w[1],P=function(){var t,e=(t=regeneratorRuntime.mark((function t(){var e;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return k(!0),t.prev=1,t.next=4,c()({path:Object(i.g)("/broadcast/".concat(r,"/stats")),method:"GET"});case 4:e=t.sent,j(e.result),t.next=11;break;case 8:t.prev=8,t.t0=t.catch(1),console.log(t.t0);case 11:k(!1);case 12:case"end":return t.stop()}}),t,null,[[1,8]])})),function(){var e=this,r=arguments;return new Promise((function(n,a){var o=t.apply(e,r);function c(t){m(o,n,a,c,i,"next",t)}function i(t){m(o,n,a,c,i,"throw",t)}c(void 0)}))});return function(){return e.apply(this,arguments)}}();Object(n.useEffect)((function(){P(),jQuery(document).ready((function(){window.dispatchEvent(new Event("resize"))}))}),[]);var S=Object(u.d)("hour"),x=h.map((function(t,e){var r={label:Object(l.__)("Opens","wp-marketing-automations"),value:parseInt(t.subtotals.opens)},n={label:Object(l.__)("Clicks","wp-marketing-automations"),value:parseInt(t.subtotals.clicks)};return function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?f(Object(r),!0).forEach((function(e){b(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):f(Object(r)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({date:Object(s.format)("Y-m-d\\TH:i:s",t.start_date),primary:1===v?r:n},1===v?{secondary:n}:{})})),_=Object(l.__)("Total Opens & Clicks","wp-marketing-automations"),E=Object(l.__)("Total Clicks","wp-marketing-automations");return Object(n.createElement)(a.a,{data:x,title:1===v?_:E,chartType:o,interval:"hour",isRequesting:g,layout:"item-comparison",xFormat:S.xFormat,x2Format:S.x2Format,screenReaderFormat:S.screenReaderFormat,tooltipLabelFormat:S.tooltipLabelFormat})}}}]);