/*************************************************************************
  This code is from Dynamic Web Coding at dyn-web.com
  Copyright 2001-5 by Sharon Paine 
  See Terms of Use at www.dyn-web.com/bus/terms.html
  regarding conditions under which you may use this code.
  This notice must be retained in the code as is!
*************************************************************************/

/* 
    dw_scrollObj.js  version date: March 2005
    GeckoTableBugFix algorithm revised, and now excludes Safari and Konqueror.
    
    dw_scrollObj.js contains constructor and basic methods for scrolling layers.
    Use with dw_hoverscroll.js and/or dw_glidescroll.js,
    and for scrollbars: dw_scroll-aux.js and dw_slidebar.js
*/

dw_scrollObjs = {};
dw_scrollObj.speed = 100; // default speed for mouseover scrolling
//  constructor arguments: id of layer containing scrolling layers (clipped layer), id of layer to scroll, 
//	id of table or other element that scrolling content is nested in. 
//	ns6+/moz need that extra container to get width for horizontal scrolling.
//	(not needed for vertical scrolling)
function dw_scrollObj(wnId, lyrId, cntId) {
    this.id = wnId; dw_scrollObjs[this.id] = this;
    this.animString = "dw_scrollObjs." + this.id;
    this.load(lyrId, cntId);
}

dw_scrollObj.loadLayer = function(wnId, id, cntId) {
    if ( dw_scrollObjs[wnId] ) dw_scrollObjs[wnId].load(id, cntId);
}

dw_scrollObj.prototype.load = function(lyrId, cntId) {
    if (!document.getElementById) return;
    var wndo, lyr;
    if (this.lyrId) {
        lyr = document.getElementById(this.lyrId);
        lyr.style.visibility = "hidden";
    }
    lyr = document.getElementById(lyrId);
    wndo = document.getElementById(this.id);
    lyr.style.top = this.y = 0; lyr.style.left = this.x = 0;
    this.maxY = (lyr.offsetHeight - wndo.offsetHeight > 0)? lyr.offsetHeight - wndo.offsetHeight: 0;
    this.wd = cntId? document.getElementById(cntId).offsetWidth: lyr.offsetWidth;
    this.maxX = (this.wd - wndo.offsetWidth > 0)? this.wd - wndo.offsetWidth: 0;
    this.lyrId = lyrId; // hold id of currently visible layer
    lyr.style.visibility = "visible";
    this.on_load(); this.ready = true;
}

dw_scrollObj.prototype.on_load = function() {}  

dw_scrollObj.prototype.shiftTo = function(lyr, x, y) {
    lyr.style.left = (this.x = x) + "px"; 
    lyr.style.top = (this.y = y) + "px";
}

// remove layers from table for ns6+/mozilla (needed for scrolling inside tables)
// recent versions of ns/moz (ns7.2 and moz 1.73) don't need it (ns 7.1 and moz 1.5 do)
dw_scrollObj.GeckoTableBugFix = function() {
    var ua = navigator.userAgent;
    if ( ua.indexOf("Gecko") > -1 && ua.indexOf("Firefox") == -1 
        && ua.indexOf("Safari") == -1 && ua.indexOf("Konqueror") == -1 ) {
        dw_scrollObj.hold = []; // holds id's of wndo and its container
        for (var i=0; arguments[i]; i++) {
            if ( dw_scrollObjs[ arguments[i] ] ) {
                var wndo = document.getElementById( arguments[i] );
                var holderId = wndo.parentNode.id;
                var holder = document.getElementById(holderId);
                document.body.appendChild( holder.removeChild(wndo) );
                wndo.style.zIndex = 1000;
                var pos = getPageOffsets(holder);
                wndo.style.left = pos.x + "px"; wndo.style.top = pos.y + "px";
                dw_scrollObj.hold[i] = [ arguments[i], holderId ];
            }
        }
        window.addEventListener("resize", dw_scrollObj.rePositionGecko, true);
    }
}

// ns6+/mozilla need to reposition layers onresize when scrolling inside tables.
dw_scrollObj.rePositionGecko = function() {
    if (dw_scrollObj.hold) {
        for (var i=0; dw_scrollObj.hold[i]; i++) {
            var wndo = document.getElementById( dw_scrollObj.hold[i][0] );
            var holder = document.getElementById( dw_scrollObj.hold[i][1] );
            var pos = getPageOffsets(holder);
            wndo.style.left = pos.x + "px"; wndo.style.top = pos.y + "px";
        }
    }
}

function getPageOffsets(el) {
    var left = el.offsetLeft;
    var top = el.offsetTop;
    if ( el.offsetParent && el.offsetParent.clientLeft || el.offsetParent.clientTop ) {
        left += el.offsetParent.clientLeft;
        top += el.offsetParent.clientTop;
    }
    while ( el = el.offsetParent ) {
        left += el.offsetLeft;
        top += el.offsetTop;
    }
    return { x:left, y:top };
}
