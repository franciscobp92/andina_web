(window.webpackJsonp=window.webpackJsonp||[]).push([[12],{"33az":function(e,t,i){"use strict";i.d(t,"a",(function(){return s}));var a=i("fXoL");let s=(()=>{class e{constructor(e){this.elementRef=e}}return e.\u0275fac=function(t){return new(t||e)(a.Tb(a.l))},e.\u0275dir=a.Ob({type:e,selectors:[["","fuseWidgetToggle",""]]}),e})()},C9rh:function(e,t,i){"use strict";i.r(t),i.d(t,"ProjectDashboardModule",(function(){return te}));var a=i("tyNb"),s=i("bTqV"),o=i("f0Cb"),n=i("kmnG"),r=i("NFeN"),c=i("STbY"),d=i("d3UM"),l=i("+0xr"),b=i("wZkO"),h=i("zQsl"),f=i("5HBU"),u=i("LPQX"),p=i("4CPF"),g=i("0EQZ"),m=i("2Vo4");function w(e,t,i){e._context.bezierCurveTo((2*e._x0+e._x1)/3,(2*e._y0+e._y1)/3,(e._x0+2*e._x1)/3,(e._y0+2*e._y1)/3,(e._x0+4*e._x1+t)/6,(e._y0+4*e._y1+i)/6)}function x(e){this._context=e}x.prototype={areaStart:function(){this._line=0},areaEnd:function(){this._line=NaN},lineStart:function(){this._x0=this._x1=this._y0=this._y1=NaN,this._point=0},lineEnd:function(){switch(this._point){case 3:w(this,this._x1,this._y1);case 2:this._context.lineTo(this._x1,this._y1)}(this._line||0!==this._line&&1===this._point)&&this._context.closePath(),this._line=1-this._line},point:function(e,t){switch(e=+e,t=+t,this._point){case 0:this._point=1,this._line?this._context.lineTo(e,t):this._context.moveTo(e,t);break;case 1:this._point=2;break;case 2:this._point=3,this._context.lineTo((5*this._x0+this._x1)/6,(5*this._y0+this._y1)/6);default:w(this,e,t)}this._x0=this._x1,this._x1=e,this._y0=this._y1,this._y1=t}};var _=function(e){return new x(e)},v=i("PVWW"),y=i("wd/R"),Y=i("fXoL"),Z=i("HX77"),D=i("tk/3"),C=i("wUqA"),j=i("EnSQ");let F=(()=>{class e{constructor(e,t,i){this._httpClient=e,this._authService=t,this._dataService=i,this.widgets_ventas={widget:{title:"Spent",nro_pedidos_hoy:0,nro_pedidos_semanal:0,nro_transacciones:0,nro_pedidos_pendientes:0,ranges:{TW:"Semanal",LW:"Mensual","2W":"Anual"},total_ventas:{TW:{title:"Venta Semanal",count:0},LW:{title:"Venta Mensual",count:0},"2W":{title:"Venta Anual",count:0}},total_ventas_fecha_actual:{title:"Ventas a la Fecha",count:0},totalRemaining:{title:"Total de Ventas",count:0},totalBudget:{title:"TOTAL BUDGET",count:0}}}}resolve(e,t){return new Promise((e,t)=>{Promise.all([this.getProjects(),this.getWidgets()]).then(t=>{e(t)},t)})}getProjects(){let e=this._authService.getCurrentUser(),t="https://andinalicores.com.ec/wp-json/custom-plugin/vendedor/pedidos/"+e.code;return new Promise((i,a)=>{this._httpClient.get(t).subscribe(t=>{if(this.projects=t,this.projects.PedFacClientes.length>0){this.projects=this.projects.PedFacClientes,this.projects.sort((e,t)=>e.FEC_PEDIDO<t.FEC_PEDIDO?1:e.FEC_PEDIDO>t.FEC_PEDIDO?-1:0);var a=new Array;if("vendedor_al"==e.roles)for(let e=0;e<this.projects.length;e++)a.push({fecha:this.projects[e].FEC_PEDIDO,nombre:this.projects[e].CLI_NOMBRE,detalle:this.projects[e].TOT_PEDIDO,codigo:this.projects[e].COD_PEDIDO,estado:this.projects[e].ESTADO});this.projects=a,i(t)}i(t)},a)})}getWidgets(){return new Promise((e,t)=>{this._httpClient.get("api/project-dashboard-widgets").subscribe(t=>{this.widgets=t,e(t)},t)})}}return e.\u0275fac=function(t){return new(t||e)(Y.dc(D.b),Y.dc(C.a),Y.dc(j.a))},e.\u0275prov=Y.Pb({token:e,factory:e.\u0275fac}),e})();var S=i("XiUz"),A=i("EwFO"),O=i("uREc"),L=i("ofXK"),M=i("MMsv"),T=i("znSr");const P=function(){return{value:"50"}},W=function(){return{y:"100%"}},E=function(e){return{value:"*",params:e}};function k(e,t){if(1&e&&(Y.Zb(0,"div",12),Y.Zb(1,"fuse-widget",13),Y.Zb(2,"div",14),Y.Zb(3,"div",15),Y.Zb(4,"div",16),Y.Oc(5,"Hoy"),Y.Yb(),Y.Yb(),Y.Zb(6,"div",17),Y.Zb(7,"div",18),Y.Oc(8),Y.Yb(),Y.Zb(9,"div",19),Y.Oc(10,"Pedidos "),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb(),Y.Zb(11,"fuse-widget",13),Y.Zb(12,"div",14),Y.Zb(13,"div",15),Y.Zb(14,"div",16),Y.Oc(15,"Semanal"),Y.Yb(),Y.Yb(),Y.Zb(16,"div",17),Y.Zb(17,"div",18),Y.Oc(18),Y.Yb(),Y.Zb(19,"div",19),Y.Oc(20,"Pedidos "),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb(),Y.Zb(21,"fuse-widget",13),Y.Zb(22,"div",14),Y.Zb(23,"div",15),Y.Zb(24,"div",16),Y.Oc(25,"Transacciones"),Y.Yb(),Y.Yb(),Y.Zb(26,"div",17),Y.Zb(27,"div",18),Y.Oc(28),Y.Yb(),Y.Zb(29,"div",19),Y.Oc(30,"Pedidos "),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb(),Y.Zb(31,"fuse-widget",13),Y.Zb(32,"div",14),Y.Zb(33,"div",15),Y.Zb(34,"div",16),Y.Oc(35,"Pagos Pendientes"),Y.Yb(),Y.Yb(),Y.Zb(36,"div",17),Y.Zb(37,"div",20),Y.Oc(38),Y.Yb(),Y.Zb(39,"div",19),Y.Oc(40,"No cancelados "),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb()),2&e){const e=Y.lc();Y.sc("@animateStagger",Y.uc(9,P)),Y.Fb(1),Y.sc("@animate",Y.vc(11,E,Y.uc(10,W))),Y.Fb(7),Y.Qc(" ",e.widget_ventas.widget.nro_pedidos_hoy," "),Y.Fb(3),Y.sc("@animate",Y.vc(14,E,Y.uc(13,W))),Y.Fb(7),Y.Qc(" ",e.widget_ventas.widget.nro_pedidos_semanal," "),Y.Fb(3),Y.sc("@animate",Y.vc(17,E,Y.uc(16,W))),Y.Fb(7),Y.Qc(" ",e.widget_ventas.widget.nro_transacciones," "),Y.Fb(3),Y.sc("@animate",Y.vc(20,E,Y.uc(19,W))),Y.Fb(7),Y.Qc(" ",e.widget_ventas.widget.nro_pedidos_pendientes," ")}}function I(e,t){1&e&&(Y.Zb(0,"mat-tab",21),Y.Zb(1,"div",22),Y.Ub(2,"iframe",23),Y.Yb(),Y.Yb())}function R(e,t){1&e&&(Y.Zb(0,"mat-header-cell"),Y.Oc(1," Fecha Pedido "),Y.Yb())}function N(e,t){if(1&e&&(Y.Zb(0,"mat-cell"),Y.Oc(1),Y.mc(2,"date"),Y.Yb()),2&e){const e=t.$implicit;Y.Fb(1),Y.Qc(" ",Y.oc(2,1,e.fecha,"medium")," ")}}function z(e,t){1&e&&(Y.Zb(0,"mat-header-cell"),Y.Oc(1," Nombre "),Y.Yb())}function Q(e,t){if(1&e&&(Y.Zb(0,"mat-cell"),Y.Oc(1),Y.Yb()),2&e){const e=t.$implicit;Y.Fb(1),Y.Qc(" ",e.nombre," ")}}function B(e,t){1&e&&(Y.Zb(0,"mat-header-cell"),Y.Oc(1," Detalle "),Y.Yb())}function X(e,t){if(1&e&&(Y.Zb(0,"mat-cell"),Y.Oc(1),Y.mc(2,"currency"),Y.Yb()),2&e){const e=t.$implicit;Y.Fb(1),Y.Qc(" ",Y.pc(2,1,e.detalle,"USD","symbol")," ")}}function H(e,t){1&e&&(Y.Zb(0,"mat-header-cell",37),Y.Oc(1," Codigo "),Y.Yb())}function U(e,t){if(1&e&&(Y.Zb(0,"mat-cell",37),Y.Oc(1),Y.Yb()),2&e){const e=t.$implicit;Y.Fb(1),Y.Qc(" ",e.codigo," ")}}function V(e,t){1&e&&Y.Ub(0,"mat-header-row")}function J(e,t){1&e&&Y.Ub(0,"mat-row")}function $(e,t){if(1&e&&(Y.Zb(0,"div",24),Y.Zb(1,"fuse-widget",25),Y.Zb(2,"div",14),Y.Zb(3,"mat-table",26),Y.Xb(4,27),Y.Mc(5,R,2,0,"mat-header-cell",28),Y.Mc(6,N,3,4,"mat-cell",29),Y.Wb(),Y.Xb(7,30),Y.Mc(8,z,2,0,"mat-header-cell",28),Y.Mc(9,Q,2,1,"mat-cell",29),Y.Wb(),Y.Xb(10,31),Y.Mc(11,B,2,0,"mat-header-cell",28),Y.Mc(12,X,3,5,"mat-cell",29),Y.Wb(),Y.Xb(13,32),Y.Mc(14,H,2,0,"mat-header-cell",33),Y.Mc(15,U,2,1,"mat-cell",34),Y.Wb(),Y.Mc(16,V,1,0,"mat-header-row",35),Y.Mc(17,J,1,0,"mat-row",36),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb()),2&e){const e=Y.lc();Y.sc("@animateStagger",Y.uc(5,P)),Y.Fb(1),Y.sc("@animate",Y.vc(7,E,Y.uc(6,W))),Y.Fb(2),Y.sc("dataSource",e.dataSource),Y.Fb(13),Y.sc("matHeaderRowDef",e.displayedColumns),Y.Fb(1),Y.sc("matRowDefColumns",e.displayedColumns)}}const q=function(){return{x:"50px"}};let G=(()=>{class e{constructor(e,t,i,a){this._fuseSidebarService=e,this._projectDashboardService=t,this._dataService=i,this._authService=a,this.widget5={},this.widget6={},this.widget7={},this.widget8={},this.widget9={},this.widget11={},this.dateNow=Date.now(),this.displayedColumns=["fecha","nombre","detalle","codigo"],this.dataSource=[],this.user=this._authService.getCurrentUser(),this.widget5={currentRange:"TW",xAxis:!0,yAxis:!0,gradient:!1,legend:!1,showXAxisLabel:!1,xAxisLabel:"Days",showYAxisLabel:!1,yAxisLabel:"Isues",scheme:{domain:["#42BFF7","#C6ECFD","#C7B42C","#AAAAAA"]},onSelect:e=>{console.log(e)},supporting:{currentRange:"",xAxis:!1,yAxis:!1,gradient:!1,legend:!1,showXAxisLabel:!1,xAxisLabel:"Days",showYAxisLabel:!1,yAxisLabel:"Isues",scheme:{domain:["#42BFF7","#C6ECFD","#C7B42C","#AAAAAA"]},curve:_}},this.widget6={currentRange:"TW",legend:!1,explodeSlices:!1,labels:!0,doughnut:!0,gradient:!1,scheme:{domain:["#f44336","#9c27b0","#03a9f4","#e91e63"]},onSelect:e=>{console.log(e)}},this.widget7={currentRange:"T"},this.widget8={legend:!1,explodeSlices:!1,labels:!0,doughnut:!1,gradient:!1,scheme:{domain:["#f44336","#9c27b0","#03a9f4","#e91e63","#ffc107"]},onSelect:e=>{console.log(e)}},this.widget9={currentRange:"TW",xAxis:!1,yAxis:!1,gradient:!1,legend:!1,showXAxisLabel:!1,xAxisLabel:"Days",showYAxisLabel:!1,yAxisLabel:"Isues",scheme:{domain:["#42BFF7","#C6ECFD","#C7B42C","#AAAAAA"]},curve:_},setInterval(()=>{this.dateNow=Date.now()},1e3)}ngOnInit(){this.projects=this._projectDashboardService.projects,this.selectedProject=this.projects[0],this.widgets=this._projectDashboardService.widgets,this.widget11.onContactsChanged=new m.a({}),this.widget11.onContactsChanged.next(this.widgets.widget11.table.rows),this.widget11.dataSource=new K(this.widget11),this.widget_ventas=this._projectDashboardService.widgets_ventas,this.loadWidgetData()}loadWidgetData(){let e=(0==y().day()?7:y().day())-1,t=y().subtract(e,"day").format("YYYY-MM-DD"),i=y().format("YYYY-MM-DD");this.projects.forEach(e=>{let a=y(e.fecha).format("YYYY-MM-DD");a==i&&(this.widget_ventas.widget.nro_pedidos_hoy+=1),a<t&&(this.widget_ventas.widget.nro_pedidos_semanal+=1),"Mayorizada"==e.estado&&(this.widget_ventas.widget.nro_transacciones+=1),"Mayorizada"!=e.estado&&(this.widget_ventas.widget.nro_pedidos_pendientes+=1),a<=t&&(this.widget_ventas.widget.total_ventas.TW.count+=e.detalle),y(a).month()<y(t).month()&&(this.widget_ventas.widget.total_ventas.LW.count+=e.detalle),y(a).year()<y(t).year()&&(this.widget_ventas.widget.total_ventas["2W"].count+=e.detalle),a<=i&&(this.widget_ventas.widget.total_ventas_fecha_actual.count+=e.detalle),this.widget_ventas.widget.totalBudget.count+=e.detalle}),this.dataSource=this.projects.splice(0,5)}toggleSidebar(e){this._fuseSidebarService.getSidebar(e).toggleOpen()}}return e.\u0275fac=function(t){return new(t||e)(Y.Tb(Z.a),Y.Tb(F),Y.Tb(j.a),Y.Tb(C.a))},e.\u0275cmp=Y.Nb({type:e,selectors:[["project-dashboard"]],decls:15,vars:5,consts:[["id","dashboard-project","fxLayout","row",1,"page-layout","simple","right-sidebar"],["fusePerfectScrollbar","",1,"center"],["fxLayout","column","fxLayoutAlign","space-between",1,"header","p-24","pb-0",2,"background-color","#5d9ed1","color","#ffffff","border","none"],["fxLayout","row","fxLayoutAlign","space-between start"],[1,"mat-display-1","my-0","my-sm-24","welcome-message"],[1,"content"],["dynamicHeight",""],["label","Historial"],["class","widget-group p-12","fxLayout","row wrap","fxFlex","100",4,"fuseIfOnDom"],["label","Reportes",4,"ngIf"],["label","Pedidos Recientes"],["class","widget-group","fxLayout","row wrap","fxFlex","100",4,"fuseIfOnDom"],["fxLayout","row wrap","fxFlex","100",1,"widget-group","p-12"],["fxLayout","column","fxFlex","100","fxFlex.gt-xs","50","fxFlex.gt-md","25",1,"widget"],[1,"fuse-widget-front"],["fxLayout","row","fxLayoutAlign","space-between center",1,"pl-16","pr-8","py-16","h-52"],[1,"h3"],["fxLayout","column","fxLayoutAlign","center center",1,"pt-8","pb-32"],[1,"light-blue-fg","font-size-80","line-height-80"],[1,"h3","secondary-text","font-weight-500"],[1,"red-fg","font-size-80","line-height-80"],["label","Reportes"],[2,"width","100%","height","800px","padding","10px"],["src","https://app.powerbi.com/view?r=eyJrIjoiYTRhNWRkZDQtOTdhNS00ZTk3LWJhNWItYmM4MzdhNWVhMzJkIiwidCI6IjFjYjA2YzdiLTk3ZWEtNDNmNy1iOTQwLWI2MzZlMjBlZWM2MSIsImMiOjR9&pageName=ReportSectiona6f178ac2359d4209ec3","frameborder","0","allowFullScreen","true",2,"width","100%","height","100%"],["fxLayout","row wrap","fxFlex","100",1,"widget-group"],["fxLayout","row","fxFlex","100",1,"widget"],[1,"mat-elevation-z8",3,"dataSource"],["matColumnDef","fecha"],[4,"matHeaderCellDef"],[4,"matCellDef"],["matColumnDef","nombre"],["matColumnDef","detalle"],["matColumnDef","codigo"],["fxHide","","fxShow.gt-sm","",4,"matHeaderCellDef"],["fxHide","","fxShow.gt-sm","",4,"matCellDef"],[4,"matHeaderRowDef"],[4,"matRowDef","matRowDefColumns"],["fxHide","","fxShow.gt-sm",""]],template:function(e,t){1&e&&(Y.Zb(0,"div",0),Y.Zb(1,"div",1),Y.Zb(2,"div",2),Y.Zb(3,"div",3),Y.Zb(4,"span",4),Y.Oc(5,"Escritorio "),Y.Yb(),Y.Yb(),Y.Yb(),Y.Zb(6,"div",5),Y.Zb(7,"mat-tab-group",6),Y.Zb(8,"mat-tab",7),Y.Mc(9,k,41,22,"div",8),Y.Yb(),Y.Mc(10,I,3,0,"mat-tab",9),Y.Yb(),Y.Yb(),Y.Zb(11,"div",5),Y.Zb(12,"mat-tab-group",6),Y.Zb(13,"mat-tab",10),Y.Mc(14,$,18,9,"div",11),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb(),Y.Yb()),2&e&&(Y.Fb(4),Y.sc("@animate",Y.vc(3,E,Y.uc(2,q))),Y.Fb(6),Y.sc("ngIf","vendedor_al"==t.user.roles))},directives:[S.c,A.a,S.b,b.b,b.a,O.a,L.t,S.a,M.a,l.j,l.c,l.e,l.b,l.g,l.i,l.d,l.a,T.b,l.f,l.h],pipes:[L.f,L.d],styles:["#dashboard-project>.sidebar{width:280px;min-width:280px;max-width:280px}#dashboard-project>.center>.header{height:160px;min-height:160px;max-height:160px}#dashboard-project>.center>.header .selected-project{background:rgba(0,0,0,.12);color:#fff;padding:8px 16px;height:40px;line-height:24px;font-size:16px;border-radius:8px 0 0 0}#dashboard-project>.center>.header .project-selector{margin-left:1px;background:rgba(0,0,0,.12);border-radius:0 8px 0 0}#dashboard-project>.center>.header .project-selector mat-icon{color:#fff}#dashboard-project>.center>.content{flex:1}#dashboard-project>.center>.content mat-tab-group{height:100%}#dashboard-project>.center>.content mat-tab-group .mat-tab-body-wrapper{flex-grow:1}#dashboard-project>.center>.content .mat-tab-label-container{padding:0 24px}#dashboard-project .widget.widget5 .gridline-path.gridline-path-horizontal{display:none}.mat-cell,.mat-footer-cell,.mat-header-cell{padding:20px}"],encapsulation:2,data:{animation:v.a}}),e})();class K extends g.b{constructor(e){super(),this._widget11=e}connect(){return this._widget11.onContactsChanged}disconnect(){}}const ee=[{path:"**",component:G,resolve:{data:F}}];let te=(()=>{class e{}return e.\u0275mod=Y.Rb({type:e}),e.\u0275inj=Y.Qb({factory:function(t){return new(t||e)},providers:[F],imports:[[a.j.forChild(ee),s.c,o.b,n.d,r.b,c.b,d.b,l.l,b.c,h.a,f.a,u.e,p.a]]}),e})()},MMsv:function(e,t,i){"use strict";i.d(t,"a",(function(){return n}));var a=i("33az"),s=i("fXoL");const o=["*"];let n=(()=>{class e{constructor(e,t){this._elementRef=e,this._renderer=t,this.flipped=!1}ngAfterContentInit(){setTimeout(()=>{this.toggleButtons.forEach(e=>{this._renderer.listen(e.elementRef.nativeElement,"click",e=>{e.preventDefault(),e.stopPropagation(),this.toggle()})})})}toggle(){this.flipped=!this.flipped}}return e.\u0275fac=function(t){return new(t||e)(s.Tb(s.l),s.Tb(s.H))},e.\u0275cmp=s.Nb({type:e,selectors:[["fuse-widget"]],contentQueries:function(e,t,i){var o;1&e&&s.Lb(i,a.a,!0),2&e&&s.zc(o=s.ic())&&(t.toggleButtons=o)},hostVars:2,hostBindings:function(e,t){2&e&&s.Jb("flipped",t.flipped)},ngContentSelectors:o,decls:1,vars:0,template:function(e,t){1&e&&(s.rc(),s.qc(0))},styles:["fuse-widget{display:block;position:relative;perspective:3000px;padding:12px}fuse-widget>div{position:relative;transform-style:preserve-3d;transition:transform 1s}fuse-widget>.fuse-widget-front{display:flex;flex-direction:column;flex:1 1 auto;position:relative;visibility:visible;width:100%;opacity:1;transform:rotateY(0deg)}fuse-widget>.fuse-widget-back,fuse-widget>.fuse-widget-front{overflow:hidden;z-index:10;border-radius:8px;transition:transform .5s ease-out 0s,visibility 0s ease-in .2s,opacity 0s ease-in .2s;-webkit-backface-visibility:hidden;backface-visibility:hidden;border:1px solid}fuse-widget>.fuse-widget-back{display:block;position:absolute;top:12px;right:12px;bottom:12px;left:12px;visibility:hidden;opacity:0;transform:rotateY(180deg)}fuse-widget>.fuse-widget-back [fuseWidgetToggle]{position:absolute;top:0;right:0}fuse-widget.flipped>.fuse-widget-front{visibility:hidden;opacity:0;transform:rotateY(180deg)}fuse-widget.flipped>.fuse-widget-back{display:block;visibility:visible;opacity:1;transform:rotateY(1turn)}fuse-widget .mat-form-field.mat-form-field-type-mat-select .mat-form-field-wrapper{padding:16px 0}fuse-widget .mat-form-field.mat-form-field-type-mat-select .mat-form-field-wrapper .mat-form-field-infix{border:none;padding:0}fuse-widget .mat-form-field.mat-form-field-type-mat-select .mat-form-field-underline{display:none}"],encapsulation:2}),e})()}}]);