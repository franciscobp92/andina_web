(window.webpackJsonp=window.webpackJsonp||[]).push([[28],{762:function(e,t,a){"use strict";var r=a(221),n=a(228),c=Object(r.a)(n.b.cartAnalytics).getStateProp,i=Object(r.a)(n.b.contactAnalytics).getStateProp,o=Object(r.a)(n.b.emailAnalytics).getStateProp,l=Object(r.a)(n.b.emailTable).getStateProp,s=Object(r.a)(n.b.engagementAnalytics).getStateProp,u=Object(r.a)(n.b.directEmailTable).getStateProp,m={getCartAnalytics:function(){return c("data")},getCartAnalyticsLoading:function(){return c("isLoading")},getContactAnalytics:function(){return i("data")},getContactAnalyticsLoading:function(){return i("isLoading")},getEmailAnalytics:function(){return o("data")},getEmailAnalyticsLoading:function(){return o("isLoading")},getEmailTableList:function(){return l("data")},getEmailTableTotal:function(){return l("total")},getEmailTableOffset:function(){return l("offset")},getEmailTableLimit:function(){return l("limit")},getEmailTableLoading:function(){return l("isLoading")},getEmailTablePage:function(){return parseInt(l("offset"))/parseInt(l("limit"))+1},getEngagementAnalytics:function(){return s("data")},getEngagementAnalyticsLoading:function(){return s("isLoading")},getDirectEmailTableList:function(){return u("data")},getDirectEmailTableTotal:function(){return u("total")},getDirectEmailTableOffset:function(){return u("offset")},getDirectEmailTableLimit:function(){return u("limit")},getDirectEmailTableLoading:function(){return u("isLoading")},getDirectEmailTablePage:function(){return parseInt(u("offset"))/parseInt(u("limit"))+1}};t.a=m},764:function(e,t,a){"use strict";var r=a(0),n=a(4),c=a(2),i=a(5),o=a.n(i),l=a(70),s=a.n(l),u=a(3),m=a(1),b=a.n(m),f=a(37),p=a(17),O=function(e){var t,a=e.delta,i=e.href,l=e.hrefType,m=e.isOpen,b=e.label,O=e.onToggle,d=e.prevLabel,v=e.prevValue,y=e.reverseTrend,j=e.selected,g=e.value,w=e.onLinkClickCallback,h=e.tileIcon,_=o()("bwf-summary-item-container",{"is-dropdown-button":O,"is-dropdown-expanded":m}),E=o()("bwf-summary-item",{"is-selected":j,"is-good-trend":y?a<0:a>0,"is-bad-trend":y?a>0:a<0}),k=a>0?Object(c.sprintf)(Object(c.__)("Up %d%% from %s","wp-marketing-automations"),a,d):Object(c.sprintf)(Object(c.__)("Down %d%% from %s","wp-marketing-automations"),Math.abs(a),d);a||(k=Object(c.sprintf)(Object(c.__)("No change from %s","wp-marketing-automations"),d));var T={className:E,"aria-current":j?"page":null};if(O||i){var N=!!O;t=N?n.Button:f.a,N?(T.onClick=O,T["aria-expanded"]=m):(T.href=i,T.role="menuitem",T.onClick=w,T.type=l)}else t="div";return Object(r.createElement)("li",{className:_},Object(r.createElement)(t,T,Object(r.createElement)(p.a,{gap:"5",align:"center"},Object(r.createElement)(p.b,null,Object(r.createElement)("div",{className:"bwf-summary-item-label"},Object(r.createElement)(n.__experimentalText,{variant:"body.small"},b)),Object(r.createElement)("div",{className:"bwf-summary-item-data"},Object(r.createElement)("div",{className:"bwf-summary-item-value"},Object(r.createElement)(n.__experimentalText,{variant:"title.small"},Object(u.isNil)(g)?Object(c.__)("-","wp-marketing-automations"):g)),a&&Object(r.createElement)(n.Tooltip,{text:Object(u.isNil)(v)?Object(c.__)("-","wp-marketing-automations"):"".concat(d," ").concat(v),position:"top center"},Object(r.createElement)("div",{className:"bwf-summary-item-delta",role:"presentation","aria-label":k},Object(r.createElement)(n.__experimentalText,{variant:"caption"},Object(u.isNil)(a)?Object(c.__)("-","wp-marketing-automations"):Object(c.sprintf)(Object(c.__)("%d%%","wp-marketing-automations"),a))))),O?Object(r.createElement)(s.a,{className:"bwf-summary-toggle",icon:"chevron-down",size:24}):null),h&&Object(r.createElement)(p.c,null,h))))};O.propTypes={delta:b.a.number,href:b.a.string,hrefType:b.a.oneOf(["wp-admin","wc-admin","external"]).isRequired,isOpen:b.a.bool,label:b.a.string.isRequired,onToggle:b.a.func,prevLabel:b.a.string,prevValue:b.a.oneOfType([b.a.number,b.a.string]),reverseTrend:b.a.bool,selected:b.a.bool,value:b.a.oneOfType([b.a.number,b.a.string]),onLinkClickCallback:b.a.func},O.defaultProps={href:"",hrefType:"wc-admin",isOpen:!1,prevLabel:Object(c.__)("Previous Period:","wp-marketing-automations"),reverseTrend:!1,selected:!1,onLinkClickCallback:u.noop},t.a=O},766:function(e,t,a){"use strict";var r=a(0),n=a(5),c=a.n(n),i=a(3),o=a(1),l=a.n(o),s=a(83),u=a(237);function m(e){return(m="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function b(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function f(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function p(e,t){for(var a=0;a<t.length;a++){var r=t[a];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function O(e,t){return(O=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}function d(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var a,r=y(e);if(t){var n=y(this).constructor;a=Reflect.construct(r,arguments,n)}else a=r.apply(this,arguments);return v(this,a)}}function v(e,t){return!t||"object"!==m(t)&&"function"!=typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function y(e){return(y=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}var j=function(e){var t=e.className,a=e.hideDelta;return Object(r.createElement)("li",{"data-testid":"summary-placeholder",className:c()("bwf-summary-item-container is-placeholder",t)},Object(r.createElement)("div",{className:"bwf-summary-item"},Object(r.createElement)("div",{className:"bwf-summary-item-label"}),Object(r.createElement)("div",{className:"bwf-summary-item-data"},Object(r.createElement)("div",{className:"bwf-summary-item-value"}),!a&&Object(r.createElement)("div",{className:"bwf-summary-item-delta"}))))},g=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&O(e,t)}(l,e);var t,a,n,o=d(l);function l(){return f(this,l),o.apply(this,arguments)}return t=l,(a=[{key:"render",value:function(){var e,t=this.props,a=t.isDropdownBreakpoint,n=t.hideDelta,o=a?1:this.props.numberOfItems,l=Object(u.a)(o),s=c()("bwf-summary",(b(e={},l,!a),b(e,"is-placeholder",!0),e));return Object(r.createElement)("ul",{className:s,"aria-hidden":"true"},Object(i.range)(o).map((function(e){return Object(r.createElement)(j,{hideDelta:n,key:e})})))}}])&&p(t.prototype,a),n&&p(t,n),l}(r.Component);g.propTypes={numberOfItems:l.a.number.isRequired},g.defaultProps={numberOfRows:5},t.a=Object(s.withViewportMatch)({isDropdownBreakpoint:"< large"})(g)},769:function(e,t,a){"use strict";var r=a(222),n=a(7),c=a(228),i=a(22),o=function(e){var t=0;return"automation"===e?t=1:"broadcast"===e&&(t=2),t};t.a=function(){var e=Object(r.a)(c.b.cartAnalytics).fetch,t=Object(r.a)(c.b.contactAnalytics),a=t.fetch,l=t.setStateProp,s=Object(r.a)(c.b.emailAnalytics),u=s.fetch,m=s.setStateProp,b=Object(r.a)(c.b.emailTable),f=b.fetch,p=b.setStateProp,O=Object(r.a)(c.b.engagementAnalytics),d=O.fetch,v=O.setStateProp,y=Object(r.a)(c.b.directEmailTable),j=y.fetch,g=y.setStateProp;return{fetchCartAnalytics:function(t,a,r){e("GET",Object(n.g)("/analytics/carts/")+"?"+Object(i.stringify)({after:t,before:a,interval:r}))},fetchContactAnalytics:function(e,t,r){a("GET",Object(n.g)("/analytics/contacts/")+"?"+Object(i.stringify)({after:e,before:t,interval:r}))},fetchEmailAnalytics:function(e,t,a,r,c){var l=arguments.length>5&&void 0!==arguments[5]?arguments[5]:1;u("GET",Object(n.g)("/analytics/emails/chart/")+"?"+Object(i.stringify)({after:e,before:t,interval:a,oid:r,type:o(c),mode:l}))},fetchEmailTable:function(e,t,a,r,c,l,s){var u=arguments.length>7&&void 0!==arguments[7]?arguments[7]:1;f("GET",Object(n.g)("/analytics/emails/tabular/")+"?"+Object(i.stringify)({after:a,before:r,interval:c,oid:l,type:o(s),limit:e,offset:t,mode:u}))},setEmailTableData:function(e,t){p(e,t)},fetchEngagementData:function(e,t){d("GET",Object(n.g)("/analytics/engagement")+"?"+Object(i.stringify)({after:e,before:t}))},setContactAnalyticsData:function(e,t){l(e,t)},setEmailAnalyticsData:function(e,t){m(e,t)},setEngagementData:function(e,t){v(e,t)},fetchDirectEmailTable:function(e,t,a,r,c){j("GET",Object(n.g)("/analytics/direct-emails/tabular/")+"?"+Object(i.stringify)({after:a,before:r,interval:c,limit:e,offset:t}))},setDirectEmailTableData:function(e,t){g(e,t)}}}},770:function(e,t,a){"use strict";var r=a(219),n=a(0);t.a=function(e,t,a){var c=bwfcrm_contacts_data&&bwfcrm_contacts_data.header_data?bwfcrm_contacts_data.header_data:{},i=Object(r.a)(),o=i.setActiveMultiple,l=i.resetHeaderMenu,s=i.setL2NavType,u=i.setL2Nav,m=i.setBackLink,b=i.setL2Title,f=i.setPageHeader;return Object(n.useEffect)((function(){l(),!t&&s("menu"),!t&&u(c.reports_nav),o({leftNav:"analytics-"+e,rightNav:e+"-analytics"}),t&&m(t),a&&b(a),f("Analytics")}),[e,a]),!0}},825:function(e,t,a){},826:function(e,t,a){"use strict";var r=Object.assign||function(e){for(var t,a=1;a<arguments.length;a++)for(var r in t=arguments[a])Object.prototype.hasOwnProperty.call(t,r)&&(e[r]=t[r]);return e};Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){var t=e.size,a=void 0===t?24:t,n=e.onClick,c=(e.icon,e.className),o=function(e,t){var a={};for(var r in e)0<=t.indexOf(r)||Object.prototype.hasOwnProperty.call(e,r)&&(a[r]=e[r]);return a}(e,["size","onClick","icon","className"]),l=["gridicon","gridicons-line-graph",c,!1,!1,!1].filter(Boolean).join(" ");return i.default.createElement("svg",r({className:l,height:a,width:a,onClick:n},o,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"}),i.default.createElement("g",null,i.default.createElement("path",{d:"M3 19h18v2H3zm3-3c1.1 0 2-.9 2-2 0-.5-.2-1-.5-1.3L8.8 10H9c.5 0 1-.2 1.3-.5l2.7 1.4v.1c0 1.1.9 2 2 2s2-.9 2-2c0-.5-.2-.9-.5-1.3L17.8 7h.2c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2c0 .5.2 1 .5 1.3L15.2 9H15c-.5 0-1 .2-1.3.5L11 8.2V8c0-1.1-.9-2-2-2s-2 .9-2 2c0 .5.2 1 .5 1.3L6.2 12H6c-1.1 0-2 .9-2 2s.9 2 2 2z"})))};var n,c=a(6),i=(n=c)&&n.__esModule?n:{default:n};e.exports=t.default},827:function(e,t,a){"use strict";var r=Object.assign||function(e){for(var t,a=1;a<arguments.length;a++)for(var r in t=arguments[a])Object.prototype.hasOwnProperty.call(t,r)&&(e[r]=t[r]);return e};Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){var t,a=e.size,n=void 0===a?24:a,c=e.onClick,o=(e.icon,e.className),l=function(e,t){var a={};for(var r in e)0<=t.indexOf(r)||Object.prototype.hasOwnProperty.call(e,r)&&(a[r]=e[r]);return a}(e,["size","onClick","icon","className"]),s=["gridicon","gridicons-stats-alt",o,!1,!1,(t=n,!(0!=t%18)&&"needs-offset-y")].filter(Boolean).join(" ");return i.default.createElement("svg",r({className:s,height:n,width:n,onClick:c},l,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"}),i.default.createElement("g",null,i.default.createElement("path",{d:"M21 21H3v-2h18v2zM8 10H4v7h4v-7zm6-7h-4v14h4V3zm6 3h-4v11h4V6z"})))};var n,c=a(6),i=(n=c)&&n.__esModule?n:{default:n};e.exports=t.default},917:function(e,t,a){"use strict";a.r(t);var r=a(0),n=a(2),c=a(234),i=a(764),o=a(766),l=a(762),s=a(56),u=a(7),m=a(12),b=function(e){var t=Object(s.a)(Object(u.L)()).formatAmount,a=e.query,b=l.a.getCartAnalyticsLoading,f=l.a.getCartAnalytics,p=(a.chart&&a.chart,b()),O=f(),d=function(e){if(!O||!O.hasOwnProperty("totals"))return 0;var t=O.totals;return t.hasOwnProperty(e)?t[e]:void 0};return Object(r.createElement)("div",{className:"bwf-crm-cart-report-tiles"},Object(r.createElement)(c.a,null,(function(){var e=[];return e.push(Object(r.createElement)(i.a,{key:1,value:d("recoverable_carts"),label:Object(n.__)("Recoverable Carts","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"cart-recoverable",size:30}))}),Object(r.createElement)(i.a,{key:2,value:t(d("potential_revenue")),label:Object(n.__)("Potential Revenue","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"revenue-potential",size:30}))}),Object(r.createElement)(i.a,{key:3,value:d("recovered_cart"),label:Object(n.__)("Recovered Carts","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"cart-recovered",size:30}))}),Object(r.createElement)(i.a,{key:4,value:t(d("recovered_revenue")),label:Object(n.__)("Recovered Revenue","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"revenue-recovered",size:30}))}),Object(r.createElement)(i.a,{key:5,value:d("recovery_rate")+"%",label:Object(n.__)("Recovery Rate","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"recovery-rate",size:30}))}),Object(r.createElement)(i.a,{key:6,value:d("lost_cart"),label:Object(n.__)("Lost Carts","wp-marketing-automations"),tileIcon:Object(r.createElement)("div",{className:"bwf-tile-icon-wrap"},Object(r.createElement)(m.a,{icon:"cart-lost",size:30}))})),p?Object(r.createElement)(o.a,{numberOfItems:6,hideDelta:!0}):e})))},f=a(233),p=a(26),O=function(e){var t=e.query,a=l.a.getCartAnalytics,c=l.a.getCartAnalyticsLoading,i=(t.chart&&t.chart,c()),o=a(),s=t.chartType?t.chartType:"line",u=Object(p.f)(t),m=Object(p.b)(function(e){return e.period&&null!=e.period&&""!=e.period||(e.period="month"),e.compare&&null!=e.compare&&""!=e.compare||(e.compare="previous_year"),e}(t));m.includes(u)||(u=m[m.length-1]);var b=function(e){var t=[];o&&o.hasOwnProperty("intervals")&&o.intervals.map((function(a){"cart"==e&&t.push({date:a.start_date,recoverable_carts:{label:Object(n.__)("Recoverable Carts","wp-marketing-automations"),value:parseFloat(a.subtotals.recoverable_carts)},recovered_cart:{label:Object(n.__)("Recovered Carts","wp-marketing-automations"),value:parseFloat(a.subtotals.recovered_cart)},lost_cart:{label:Object(n.__)("Lost Carts","wp-marketing-automations"),value:parseFloat(a.subtotals.lost_cart)}}),"revenue"===e&&t.push({date:a.start_date,potantial_revenue:{label:Object(n.__)("Potential Revenue","wp-marketing-automations"),value:parseFloat(a.subtotals.potential_revenue)},recovered_revenue:{label:Object(n.__)("Recovered Revenue","wp-marketing-automations"),value:parseFloat(a.subtotals.recovered_revenue)}})}));return t};return Object(r.createElement)("div",{className:"bwf-crm-cart-report-table-wrapper"},Object(r.createElement)("div",{className:"bwf-crm-cart-report-table-block"},Object(r.createElement)(f.a,{isRequesting:i,data:b("cart"),title:Object(n.__)("Carts","wp-marketing-automations"),interval:u,layout:"item-comparison",chartType:s,hideTypeSelect:!0,legendPosition:"bottom",interactiveLegend:!0})),Object(r.createElement)("div",{className:"bwf-crm-cart-report-table-block"},Object(r.createElement)(f.a,{isRequesting:i,data:b("revenue"),title:Object(n.__)("Revenue","wp-marketing-automations"),interval:u,layout:"item-comparison",chartType:s,hideTypeSelect:!0,legendPosition:"bottom",interactiveLegend:!0})))},d=a(130),v=a(30),y=(a(382),a(14));function j(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,r)}return a}function g(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?j(Object(a),!0).forEach((function(t){w(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):j(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function w(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}var h=function(e){var t=e.query;return Object(r.createElement)("div",{className:"bwf-crm-cart-report-filter"},Object(r.createElement)("div",{className:"bwf_clear_20"}),Object(r.createElement)(v.b,{component:"div",className:"bwf-filters"},Object(r.createElement)("div",{className:"bwfcrm-filters-basic-filters"},Object(r.createElement)(d.a,{key:JSON.stringify(t),onRangeSelect:function(e){var a=g(g({},t),e);delete a.compare,Object(y.j)(a,"/",{})},dateQuery:function(e){e.compare="previous_year";var t=Object(p.e)(e),a=t.period,r=t.compare,n=t.before,c=t.after,i=Object(p.c)(e);return{period:a,compare:r,before:n,after:c,primaryDate:i.primary,secondaryDate:i.secondary}}(t),isoDateFormat:p.g,hideCompare:!0}))))},_=a(769),E=a(3),k=(a(825),a(5)),T=a.n(k),N=a(4),P=a(236),C=a(826),L=a.n(C),D=a(827),A=a.n(D),R=a(770),S=a(254),x=a(79);t.default=function(e){var t=Object(_.a)().fetchCartAnalytics;Object(R.a)("carts",!1,"");var a=Object(y.h)(),c=a.chartType?a.chartType:"line",i=function(e,a){var r=function(e){return e.compare="previous_year",Object(p.c)(e).primary}(e),n="",c="";r&&(Object(E.isEmpty)(r.after)||(n=r.after.format("YYYY-MM-DD HH:mm:ss")),Object(E.isEmpty)(r.before)||(c=r.before.format("YYYY-MM-DD 23:59:59")),Object(E.isEmpty)(n)||Object(E.isEmpty)(c)||t(n,c,a))};a.hasOwnProperty("period")||(a.period="month");var o=a.period,l=a.compare,s=a.interval,m=a.after,f=a.before;Object(r.useEffect)((function(){Object(u.d)("Cart Analytics");var e=a.hasOwnProperty("interval")?a.interval:"day";i(a,e)}),[o,l,s,m,f]);var d=function(e){delete a.compare,Object(y.j)({chartType:e},"/",a)},v=function(e){delete a.compare,Object(y.j)({interval:e},"/",a)};return Object(u.T)()?Object(r.createElement)(r.Fragment,null,Object(r.createElement)(x.a,null),Object(r.createElement)("div",{className:"bwf-crm-analytics-wrap bwf-crm-cart-report-wrap"},Object(r.createElement)(h,{query:a}),Object(r.createElement)("div",{className:"bwf_clear_20"}),Object(r.createElement)(P.a,{className:"bwf-cart-report-header-chart",title:Object(n.__)("Performance","wp-marketing-automations")}),Object(r.createElement)(b,{query:a}),Object(r.createElement)(P.a,{className:"bwf-cart-report-header-chart has-interval-select",title:Object(n.__)("Charts","wp-marketing-automations")},function(e){var t=e.chartInterval,a=e.query,c=Object(p.b)(a);if(!c||c.length<1)return null;var i={hour:Object(n.__)("By hour","wp-marketing-automations"),day:Object(n.__)("By day","wp-marketing-automations"),week:Object(n.__)("By week","wp-marketing-automations"),month:Object(n.__)("By month","wp-marketing-automations"),quarter:Object(n.__)("By quarter","wp-marketing-automations"),year:Object(n.__)("By year","wp-marketing-automations")};return Object(r.createElement)(N.SelectControl,{className:"bwf-chart__interval-select",value:t,options:c.map((function(e){return{value:e,label:i[e]}})),onChange:v})}({interval:s,query:a}),Object(r.createElement)(N.NavigableMenu,{className:"bwf-chart__types",orientation:"horizontal",role:"menubar"},Object(r.createElement)(N.Button,{className:T()("bwf-chart__type-button",{"bwf-chart__type-button-selected":!c||"line"===c}),title:Object(n.__)("Line chart","wp-marketing-automations"),"aria-checked":"line"===c,role:"menuitemradio",tabIndex:"line"===c?0:-1,onClick:function(){return d("line")}},Object(r.createElement)(L.a,null)),Object(r.createElement)(N.Button,{className:T()("bwf-chart__type-button",{"bwf-chart__type-button-selected":"bar"===c}),title:Object(n.__)("Bar chart","wp-marketing-automations"),"aria-checked":"bar"===c,role:"menuitemradio",tabIndex:"bar"===c?0:-1,onClick:function(){return d("bar")}},Object(r.createElement)(A.a,null)))),Object(r.createElement)(O,{query:a}))):Object(r.createElement)(S.a,{type:"bwf-cart-tracking"})}}}]);