// JavaScript Document
var $ = function (id) { 
return "string" == typeof id ? document.getElementById(id) : id; 
}; 
var Class = { 
create: function() { 
return function() { 
this.initialize.apply(this, arguments); 
} 
} 
} 
Object.extend = function(destination, source) { 
for (var property in source) { 
destination[property] = source[property]; 
} 
return destination; 
} 
function addEventHandler(oTarget, sEventType, fnHandler) { 
if (oTarget.addEventListener) { 
oTarget.addEventListener(sEventType, fnHandler, false); 
} else if (oTarget.attachEvent) { 
oTarget.attachEvent("on" + sEventType, fnHandler); 
} else { 
oTarget["on" + sEventType] = fnHandler; 
} 
}; 
var Scroller = Class.create(); 
Scroller.prototype = { 
initialize: function(idScroller, idScrollMid, options) { 
var oThis = this, oScroller = $(idScroller), oScrollMid = $(idScrollMid); 
this.SetOptions(options); 
this.Side = this.options.Side || ["up"];//方向 
this.scroller = oScroller; //对象 
this.speed = this.options.Speed; //速度 
this.timer = null; //时间 
this.pauseHeight = 0; //定高 
this.pauseWidth = 0; //定宽 
this.pause = 0; //定高(宽) 
this.side = 0; //参数 
//用于上下滚动 
this.heightScroller = parseInt(oScroller.style.height) || oScroller.offsetHeight; 
this.heightList = oScrollMid.offsetHeight; 
//用于左右滚动 
this.widthScroller = parseInt(oScroller.style.width) || oScroller.offsetWidth; 
this.widthList = oScrollMid.offsetWidth; 
//js取不到css设置的height和width 
oScroller.style.overflow = "hidden"; 
oScrollMid.appendChild(oScrollMid.cloneNode(true)); 
oScrollMid.appendChild(oScrollMid.cloneNode(true)); 
addEventHandler(oScroller, "mouseover", function() { oThis.Stop(); }); 
addEventHandler(oScroller, "mouseout", function() { oThis.Start(); }); 
this.Start(); 
}, 
//设置默认属性 
SetOptions: function(options) { 
this.options = {//默认值 
Step: 1,//每次变化的px量 
Speed: 20,//速度(越大越慢) 
Side: ["up"],//滚动方向:"up"是上，"down"是下，"left"是左，"right"是右 
PauseHeight: 0,//隔多高停一次 
PauseWidth: 0,//隔多宽停一次 
//当上下和左右一起使用时必须设置PauseHeight和PauseWidth来设置转向位置 
PauseStep: 3000//停顿时间(PauseHeight或PauseWidth大于0该参数才有效) 
}; 
Object.extend(this.options, options || {}); 
}, 
//转向 
Turn: function() { 
//通过设置方向数组的排列来转向 
this.Side.push(this.Side.shift().toLowerCase()); 
}, 
//上下滚动 
ScrollUpDown: function() { 
this.pause = this.pauseHeight; 
this.scroller.scrollTop = this.GetScroll(this.scroller.scrollTop, this.heightScroller, this.heightList, 
this.options.PauseHeight); 
this.pauseHeight = this.pause; 
var oThis = this; 
this.timer = window.setTimeout(function(){ oThis.Start(); }, this.speed); 
}, 
//左右滚动 
ScrollLeftRight: function() { 
this.pause = this.pauseWidth; 
//注意:scrollLeft超过1400会自动变回1400 注意长度 
this.scroller.scrollLeft = this.GetScroll(this.scroller.scrollLeft, this.widthScroller, this.widthList, 
this.options.PauseWidth); 
this.pauseWidth = this.pause; 
var oThis = this; 
this.timer = window.setTimeout(function(){ oThis.Start(); }, this.speed); 
}, 
//获取设置滚动数据 
GetScroll: function(iScroll, iScroller, iList, iPause) { 
var iStep = this.options.Step * this.side; 
if(this.side > 0){ 
if(iScroll >= (iList * 2 - iScroller)){ iScroll -= iList; } 
} else { 
if(iScroll <= 0){ iScroll += iList; } 
} 
this.speed = this.options.Speed; 
if(iPause > 0){ 
if(Math.abs(this.pause) >= iPause){ 
this.speed = this.options.PauseStep; this.pause = iStep = 0; this.Turn(); 
} else { 
this.pause += iStep; 
} 
} 
return (iScroll + iStep); 
}, 
//开始 
Start: function() { 
//document.getElementById("test").innerHTML+=sTurn+","; 
//方向设置 
switch (this.Side[0].toLowerCase()) { 
case "right" : 
if(this.widthList < this.widthScroller) return; 
this.side = -1; 
this.ScrollLeftRight(); 
break; 
case "left" : 
if(this.widthList < this.widthScroller) return; 
this.side = 1; 
this.ScrollLeftRight(); 
break; 
case "down" : 
if(this.heightList < this.heightScroller) return; 
this.side = -1; 
this.ScrollUpDown(); 
break; 
case "up" : 
default : 
if(this.heightList < this.heightScroller) return; 
this.side = 1; 
this.ScrollUpDown(); 
} 
}, 
//停止 
Stop: function() { 
clearTimeout(this.timer); 
} 
}; 