(window.webpackJsonp=window.webpackJsonp||[]).push([[17],{760:function(e,t,n){"use strict";var r=n(219),a=n(0),c=n(2);t.a=function(e,t,n){var o=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"",i=bwfcrm_contacts_data&&bwfcrm_contacts_data.header_data?bwfcrm_contacts_data.header_data:{},s=bwfcrm_contacts_data&&bwfcrm_contacts_data.contacts_count?parseInt(bwfcrm_contacts_data.contacts_count):0,l=Object(r.a)(),u=l.setActiveMultiple,m=l.resetHeaderMenu,b=l.setL2NavType,f=l.setL2Nav,p=l.setBackLink,O=l.setL2Title,d=l.setL2Content,g=l.setBackLinkLabel,j=l.setPageHeader;return Object(a.useEffect)((function(){m(),!t&&s>0&&b("menu"),!t&&s>0&&f(i.contacts_nav),u({leftNav:"contacts",rightNav:e}),t&&p(t),t&&g("All Contacts"),n&&O(n),n&&"Export"===n&&(o&&d(o),b("menu"),f({export:{name:Object(c.__)("All","wp-marketing-automations"),link:"admin.php?page=autonami&path=/export"}})),!t&&s>0&&o&&d(o),j("Contacts")}),[e,n]),!0}},844:function(e,t,n){},845:function(e,t,n){},872:function(e,t,n){"use strict";n.r(t);var r=n(0),a=n(22),c=(n(844),n(2)),o=n(5),i=n.n(o),s=n(3),l=n(13),u=n.n(l),m=n(4),b=n(17),f=n(220),p=n(46),O=n(122),d=n(7),g=n(35),j=n(40),w=n.n(j),y=n(104),h=n.n(y),v=n(45),_=n.n(v),E=n(14),k=n(16),P=n.n(k);function C(e,t,n,r,a,c,o){try{var i=e[c](o),s=i.value}catch(e){return void n(e)}i.done?t(s):Promise.resolve(s).then(r,a)}var x=function(e){return e.name},D={name:"lists",className:"bwf-search-bwf-lists-result",options:function(e){return(t=regeneratorRuntime.mark((function e(t){var n,r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!w()(t)){e.next=2;break}return e.abrupt("return",[]);case 2:return n={search:t,limit:5,offset:0},e.next=5,u()({path:Object(d.g)("/lists?"+Object(a.stringify)(n)),method:"GET"});case 5:return r=e.sent,e.abrupt("return",_()(r,"result")?r.result:[]);case 7:case"end":return e.stop()}}),e)})),n=function(){var e=this,n=arguments;return new Promise((function(r,a){var c=t.apply(e,n);function o(e){C(c,r,a,o,i,"next",e)}function i(e){C(c,r,a,o,i,"throw",e)}o(void 0)}))},function(e){return n.apply(this,arguments)})(e);var t,n},isDebounced:!0,getOptionIdentifier:function(e){return e.ID},getOptionKeywords:function(e){return[e.name]},getFreeTextOptions:function(e,t){return[{key:"name",label:Object(r.createElement)("span",{key:"name",className:"bwf-search-result-name"},P()({mixedString:Object(c.__)("All list with names that include {{query /}}","wp-marketing-automations"),components:{query:Object(r.createElement)("strong",{className:"components-form-token-field__suggestion-match"},e)}})),value:{id:e,name:e,lists:t.map((function(e){return _()(e,"value")?e.value:e})),searchTerm:e}}]},getOptionLabel:function(e,t){var n=Object(d.e)(x(e),t)||{};return Object(r.createElement)("span",{key:"name",className:"bwf-search-result-name","aria-label":x(e)},n.suggestionBeforeMatch,Object(r.createElement)("strong",{className:"components-form-token-field__suggestion-match"},n.suggestionMatch),n.suggestionAfterMatch)},getOptionCompletion:function(e){return e}};function N(e,t,n,r,a,c,o){try{var i=e[c](o),s=i.value}catch(e){return void n(e)}i.done?t(s):Promise.resolve(s).then(r,a)}var S=function(e){var t=e.query,n=t.hasOwnProperty("s")?t.s:"",a=w()(n)?[]:[{key:n,label:Object(c.__)("Search List: ","wp-marketing-automations")+n,bwfLabelSource:"bwfcrm_lists",isSearchTerm:!0}],o=function(){var e,n=(e=regeneratorRuntime.mark((function e(n){var r,a,c,o;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(h()(n)){e.next=2;break}return e.abrupt("return");case 2:if(w()(n)||(r=n[n.length-1],(n=[])[0]=r),a=n.find((function(e){return _()(e,"searchTerm")})),!(Object(s.isUndefined)(a)&&n.length>0&&n[0].hasOwnProperty("name"))){e.next=7;break}return Object(E.j)({s:n[0].name},"/manage/lists",t),e.abrupt("return");case 7:if(c=Object(s.isUndefined)(a)?void 0:a.searchTerm,o=_()(t,"s")&&!w()(t.s)?t.s:"",c!==o){e.next=11;break}return e.abrupt("return");case 11:Object(E.j)({s:c},"/manage/lists",t);case 12:case"end":return e.stop()}}),e)})),function(){var t=this,n=arguments;return new Promise((function(r,a){var c=e.apply(t,n);function o(e){N(c,r,a,o,i,"next",e)}function i(e){N(c,r,a,o,i,"throw",e)}o(void 0)}))});return function(e){return n.apply(this,arguments)}}();return Object(r.createElement)(g.a,{autocompleter:D,multiple:!1,allowFreeTextSearch:!0,inlineTags:!0,selected:a,onChange:o,placeholder:Object(c.__)("Search by name","wp-marketing-automations"),showClearButton:!0,disabled:!1})},I=n(221);function T(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function L(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?T(Object(n),!0).forEach((function(t){A(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):T(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function A(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function B(e,t){if(null==e)return{};var n,r,a=function(e,t){if(null==e)return{};var n,r,a={},c=Object.keys(e);for(r=0;r<c.length;r++)n=c[r],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);for(r=0;r<c.length;r++)n=c[r],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var R=function(){var e=Object(I.a)("listdata"),t=e.getStateProp;return L(L({},B(e,["getStateProp"])),{},{getLists:function(){return t("lists")},getPageNumber:function(){return parseInt(t("offset"))/parseInt(t("limit"))+1},getPerPageCount:function(){return parseInt(t("limit"))},getTotalCount:function(){return parseInt(t("total"))},getCountData:function(){return t("countData")},getContactCountData:function(){return t("contactCountData")}})},M=n(222);function q(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function F(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?q(Object(n),!0).forEach((function(t){V(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):q(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function V(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function H(e,t){if(null==e)return{};var n,r,a=function(e,t){if(null==e)return{};var n,r,a={},c=Object.keys(e);for(r=0;r<c.length;r++)n=c[r],t.indexOf(n)>=0||(a[n]=e[n]);return a}(e,t);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);for(r=0;r<c.length;r++)n=c[r],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(a[n]=e[n])}return a}var K=function(){var e=Object(M.a)("listdata"),t=e.fetch,n=e.setStateProp;return F(F({},H(e,["fetch","setStateProp"])),{},{fetch:function(e,n,r){var a=arguments.length>3&&void 0!==arguments[3]&&arguments[3],c=e.s,o=(e.page,e.filter,e.path,H(e,["s","page","filter","path"])),i={offset:n,limit:r,search:c,filters:o,get_wc:Object(d.fb)(),grab_totals:a};t("GET",Object(d.g)("/lists"),i)},setStateListValues:function(e){n("lists",e)},setStateListValuesByKey:function(e,t){n(e,t)}})},Q=(n(845),n(80)),G=n(81),U=n(34),J=n(12),X=n(760),$=n(126),z=n(219);function W(e,t,n,r,a,c,o){try{var i=e[c](o),s=i.value}catch(e){return void n(e)}i.done?t(s):Promise.resolve(s).then(r,a)}function Y(e){return function(){var t=this,n=arguments;return new Promise((function(r,a){var c=e.apply(t,n);function o(e){W(c,r,a,o,i,"next",e)}function i(e){W(c,r,a,o,i,"throw",e)}o(void 0)}))}}function Z(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function ee(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?Z(Object(n),!0).forEach((function(t){te(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Z(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function te(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function ne(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"==typeof Symbol||!(Symbol.iterator in Object(e)))return;var n=[],r=!0,a=!1,c=void 0;try{for(var o,i=e[Symbol.iterator]();!(r=(o=i.next()).done)&&(n.push(o.value),!t||n.length!==t);r=!0);}catch(e){a=!0,c=e}finally{try{r||null==i.return||i.return()}finally{if(a)throw c}}return n}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return re(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return re(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function re(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}var ae=function(e){var t=e.query,n=R(),o=K(),l=ne(Object(r.useState)(!1),2),g=l[0],j=l[1],w=ne(Object(r.useState)(!1),2),y=w[0],h=w[1],v=ne(Object(r.useState)({}),2),_=v[0],k=v[1],P=o.fetch,C=o.setStateListValues,x=o.setStateListValuesByKey,D=ne(Object(r.useState)(!1),2),N=D[0],I=D[1],T=n.getLists,L=n.getPageNumber,A=n.getPerPageCount,B=n.getLoading,M=n.getTotalCount,q=n.getCountData,F=n.getContactCountData,V=Object(r.useContext)(d.b),H=T(),W=L(),Z=A(),te=M(),re=B(),ae=q(),ce=F(),oe=ne(Object(r.useState)({}),2),ie=oe[0],se=oe[1];Object(r.useEffect)((function(){se(ce)}),[ce]),Object(r.useEffect)((function(){P(t,0,25,!0),j(!1)}),[t.s]);var le=Object(r.createElement)(m.Button,{isPrimary:!0,key:"add",className:"bwf-display-flex",onClick:function(){k({}),h(!0),I(!1)}},Object(c.__)("Add New","wp-marketing-automations")),ue=Object($.a)().getPageCountData,me=Object(z.a)().setPageCountData,be=ue();Object(r.useEffect)((function(){me(ee(ee({},be),ae))}),[ae]),Object(X.a)("manage_lists","",Object(c.__)("ALL LISTS","wp-marketing-automations"),le),Object(r.useEffect)((function(){if(!g&&!Object(s.isEmpty)(H)&&!re)try{var e={list_ids:[]};H.map((function(t){e.list_ids.push(t.ID)})),u()({method:"GET",path:Object(d.g)("/lists/contacts?"+Object(a.stringify)(e))}).then((function(e){200==e.code&&(x("contactCountData",ee(ee({},e.result),ce)),j(!0))}))}catch(e){console.log(e)}}),[H]);var fe=i()("bwfcrm-contacts-lists",{"has-search":!0}),pe=[{key:"actions",label:"",isLeftAligned:!1,cellClassName:"bwf-col-action bwf-w-30"},{key:"lists",label:Object(c.__)("Name","wp-marketing-automations"),isLeftAligned:!0},{key:"createdon",label:Object(c.__)("Created On","wp-marketing-automations"),isLeftAligned:!0},{key:"contacts",label:Object(c.__)("Contacts","wp-marketing-automations"),isLeftAligned:!0}],Oe=function(e){e!==Z&&(P(t,0,e),j(!1))},de=function(e){return Object(r.createElement)(p.a,{label:Object(c.__)("Quick Actions","wp-marketing-automations"),menuPosition:"bottom right",renderContent:function(t){var n=t.onToggle;return Object(r.createElement)(r.Fragment,null,Object(r.createElement)(O.a,{isClickable:!0,onInvoke:function(){Object(E.j)({filter:"advanced",path:"/contacts","lists_any[]":e.ID},"/",{}),n()}},Object(r.createElement)(b.a,{justify:"flex-start"},Object(r.createElement)(b.c,null,Object(r.createElement)(J.a,{icon:"view"})),Object(r.createElement)(b.c,null,Object(c.__)("View Contacts","wp-marketing-automations")))),Object(r.createElement)(O.a,{isClickable:!0,onInvoke:function(){k(e),h(!0),I(!1),n()}},Object(r.createElement)(b.a,{justify:"flex-start"},Object(r.createElement)(b.c,null,Object(r.createElement)(J.a,{icon:"edit"})),Object(r.createElement)(b.c,null,Object(c.__)("Edit","wp-marketing-automations")))),Object(r.createElement)(O.a,{isClickable:!0,onInvoke:function(){k(ee(ee({},_),{},{loading:!0,delete:!0,deleteid:e.ID})),h(!0),n()}},Object(r.createElement)(b.a,{justify:"flex-start"},Object(r.createElement)(b.c,null,Object(r.createElement)(J.a,{icon:"trash"})),Object(r.createElement)(b.c,null,Object(c.__)("Delete","wp-marketing-automations")))))}})},ge=H.map((function(e){var t,n;return[{display:de(e),value:"action"},{display:e.name,value:e.ID},{display:(n=e.created_at,Object(r.createElement)("div",{className:"bwf-display-flex-column"},Object(r.createElement)("span",null,Object(d.D)(n)))),value:e.created_at},{display:(t=e,g?ie.hasOwnProperty(parseInt(t.ID))&&ie[parseInt(t.ID)].contact_count>0?Object(r.createElement)("div",{className:"bwf-display-flex-column"},Object(r.createElement)("a",{onClick:function(){Object(E.j)({filter:"advanced",path:"/contacts","lists_any[]":t.ID},"/",{})},className:"bwf-a-no-underline"},parseInt(ie[parseInt(t.ID)].subscribers_count)+" of "+ie[parseInt(t.ID)].contact_count),Object(r.createElement)("span",null,Object(c.__)("Subscribed","wp-marketing-automations"))):"-":Object(r.createElement)("span",{className:"bwf-placeholder-temp bwf-w-150",title:"Loading"},"Loading")),value:""}]})),je=function(){var e=Y(regeneratorRuntime.mark((function e(n){var r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!n.ID){e.next=13;break}return r=H.map((function(e){return parseInt(e.ID)===parseInt(n.ID)?n:e})),e.prev=2,e.next=5,u()({path:Object(d.g)("/lists/".concat(n.ID,"/")),method:"POST",data:{list_name:n.name,description:n.description},headers:{"Content-Type":"application/json"}}).then((function(e){200==e.code?(h(!1),C(r),k({}),V(e.result),Object(d.Q)(V,2e3)):(k(ee(ee({},_),{},{error:!0,loading:!0,message:e.message})),setTimeout((function(){h(!1),k({})}),2e3))}));case 5:e.next=11;break;case 7:e.prev=7,e.t0=e.catch(2),k(ee(ee({},_),{},{error:!0,loading:!0,message:e.t0.message})),setTimeout((function(){h(!1),k({})}),2e3);case 11:e.next=22;break;case 13:return e.prev=13,e.next=16,u()({path:Object(d.g)("/list/"),method:"POST",data:{name:n.name,description:n.description},headers:{"Content-Type":"application/json"}}).then((function(e){200==e.code?(h(!1),P(t,(W-1)*Z,Z),k({}),j(!1),V(e.message),Object(d.Q)(V,2e3)):(k(ee(ee({},_),{},{error:!0,loading:!0,message:e.message})),setTimeout((function(){h(!1),k({})}),2e3))}));case 16:e.next=22;break;case 18:e.prev=18,e.t1=e.catch(13),k(ee(ee({},_),{},{error:!0,loading:!0,message:e.t1.message})),setTimeout((function(){h(!1),k({})}),2e3);case 22:case"end":return e.stop()}}),e,null,[[2,7],[13,18]])})));return function(t){return e.apply(this,arguments)}}(),we=function(){var e=Y(regeneratorRuntime.mark((function e(n){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!n){e.next=10;break}return e.prev=1,e.next=4,u()({path:Object(d.g)("/lists/".concat(n,"/")),method:"POST",data:{list_id:parseInt(n)},headers:{"X-HTTP-Method-Override":"DELETE"}}).then((function(e){200==e.code?(k(ee(ee({},_),{},{success:!0,loading:!0,message:e.message,delete:!0})),setTimeout((function(){h(!1),P(t,(W-1)*Z,Z),k({}),j(!1)}),2e3)):(k(ee(ee({},_),{},{error:!0,loading:!0,message:e.message})),setTimeout((function(){h(!1),k({})}),2e3))}));case 4:e.next=10;break;case 6:e.prev=6,e.t0=e.catch(1),k(ee(ee({},_),{},{error:!0,loading:!0,message:e.t0.message,delete:!0})),setTimeout((function(){h(!1),k({})}),2e3);case 10:case"end":return e.stop()}}),e,null,[[1,6]])})));return function(t){return e.apply(this,arguments)}}();return Object(r.createElement)("div",{className:"bwf-c-list-section bwf_w_full"},Object(r.createElement)(f.a,{className:fe,rows:ge,headers:pe,query:{paged:W},rowsPerPage:Z,totalRows:te?parseInt(te):0,isLoading:re,onPageChange:function(e,n){P(t,(e-1)*Z,Z),j(!1)},onQueryChange:function(e){return"per_page"!==e?function(){}:Oe},showMenu:!1,actions:[Object(r.createElement)(S,{key:"search",query:t})],rowHeader:!0,emptyMessage:Object(c.__)("No lists found","wp-marketing-automations")}),y&&Object(r.createElement)(m.Modal,{title:!_.delete&&(_.ID?Object(c.__)("Edit List","wp-marketing-automations"):Object(c.__)("Add List","wp-marketing-automations")),onRequestClose:function(){return h(!1)},className:"bwf-admin-modal "+(_.loading?"bwf-admin-modal-no-header ":" ")+(_.delete?" bwf-admin-modal-small ":"bwf-admin-modal-medium")},_.loading?_.success?Object(r.createElement)(r.Fragment,null,Object(r.createElement)("div",{className:"bwf-t-center"},Object(r.createElement)(Q.a,null),Object(r.createElement)("div",{className:"bwf-h1"},_.message))):_.error?Object(r.createElement)(r.Fragment,null,Object(r.createElement)("div",{className:"bwf-t-center"},Object(r.createElement)(G.a,null),Object(r.createElement)("div",{className:"bwf-h1"},_.message))):_.delete&&!_.deleteconfirm?Object(r.createElement)(r.Fragment,null,Object(r.createElement)("div",{className:"bwf-h4"},Object(c.__)("Delete","wp-marketing-automations")),Object(r.createElement)("div",{className:"bwf-t-center bwf-form-buttons"},Object(r.createElement)("div",{className:"bwf-h2"},Object(c.__)("Are you sure?","wp-marketing-automations")),Object(r.createElement)("div",{className:"bwf_clear_15"}),Object(r.createElement)("div",{className:"bwf-h4 bwf-h4-grey"},Object(c.__)("Once you delete this item. It will no longer available.","wp-marketing-automations")),Object(r.createElement)("div",{className:"bwf_clear_20"}),Object(r.createElement)("div",{className:"bwf_text_right"},Object(r.createElement)(m.Button,{isTertiary:!0,onClick:function(){k({}),h(!1)}},Object(c.__)("Cancel","wp-marketing-automations")),Object(r.createElement)(m.Button,{isPrimary:!0,className:"bwf-delete-btn",onClick:function(){k(ee(ee({},_),{},{loading:!0,deleteconfirm:!0,deleteid:_.deleteid,delete:!0})),we(_.deleteid)}},Object(c.__)("Delete","wp-marketing-automations"))))):Object(r.createElement)(U.a,null):Object(r.createElement)("div",{className:"bwf-form-fields",onKeyPress:function(e){"Enter"===e.key&&(Object(s.isEmpty)(_.name)||(k(ee(ee({},_),{},{loading:!0})),je(_)))}},N&&Object(r.createElement)(m.Notice,{status:"error",onRemove:function(){return I(!1)}},Object(c.__)("Name is required","wp-marketing-automations")),Object(r.createElement)(m.TextControl,{label:Object(c.__)("Name","wp-marketing-automations"),autoFocus:!0,type:"text",value:_.name?_.name:"",placeholder:Object(c.__)("Enter List Name","wp-marketing-automations"),onChange:function(e){k(ee(ee({},_),{},{name:e}))}}),Object(r.createElement)(m.TextareaControl,{label:Object(c.__)("Description","wp-marketing-automations"),type:"text",value:_.description?_.description:"",placeholder:Object(c.__)("Enter List Description","wp-marketing-automations"),onChange:function(e){k(ee(ee({},_),{},{description:e}))}}),Object(r.createElement)("div",{className:"bwf_clear_10"}),Object(r.createElement)("div",{className:"bwf_text_right"},Object(r.createElement)(m.Button,{isTertiary:!0,className:"bwf-mr-5",onClick:function(){return h(!1)}},Object(c.__)("Cancel","wp-marketing-automations")),Object(r.createElement)(m.Button,{isPrimary:!0,onClick:function(){Object(s.isEmpty)(_.name)?I(!0):(k(ee(ee({},_),{},{loading:!0})),je(_))},className:"bwf-ml-0"},_.ID?Object(c.__)("Save","wp-marketing-automations"):Object(c.__)("Add","wp-marketing-automations"))))))},ce=n(79);t.default=function(){var e=location&&location.search?Object(a.parse)(location.search.substring(1)):{};return Object(d.d)("Lists"),Object(r.createElement)(r.Fragment,null,Object(r.createElement)(ce.a,null),Object(r.createElement)(ae,{query:e}))}}}]);