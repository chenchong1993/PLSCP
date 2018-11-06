// All material copyright ESRI, All Rights Reserved, unless otherwise specified.
// See http://js.arcgis.com/3.17/esri/copyright.txt for details.
//>>built
define("esri/dijit/Attribution","dojo/_base/declare dojo/_base/lang dojo/_base/array dojo/_base/connect dojo/_base/kernel dojo/has dojo/query dojo/dom dojo/dom-attr dojo/dom-construct dojo/dom-style dojo/dom-class dojo/dom-geometry ../kernel ../lang ../SpatialReference ../geometry/webMercatorUtils ../geometry/Extent".split(" "),function(s,n,h,l,x,y,E,z,t,u,r,k,v,A,w,B,C,D){s=s(null,{declaredClass:"esri.dijit.Attribution",itemDelimiter:" | ",listClass:"esriAttributionList",itemClass:"esriAttributionItem",
lastItemClass:"esriAttributionLastItem",delimiterClass:"esriAttributionDelim",constructor:function(d,a){try{n.mixin(this,d);this._attributions={};this._pendingDfds={};this._activeLayers=[];this._sharedLayers=[];var b=this.domNode=z.byId(a),c=this.map,e="\x3cspan class\x3d'"+this.listClass+"'\x3e\x3c/span\x3e";b&&(t.set(b,"innerHTML",e),this.listNode=x.query(".esriAttributionList",b)[0],this.itemNodes={});this._eventConnections=[l.connect(c,"onLayerAdd",this,this._onLayerAdd),l.connect(c,"onLayerRemove",
this,this._onLayerRemove),l.connect(c,"onLayerSuspend",this,this._onLayerSuspend),l.connect(c,"onLayerResume",this,this._onLayerResume),l.connect(c,"onResize",this,this._adjustFocus),l.connect(c,"onExtentChange",this,this._onExtentChange)];if(c.loaded){var f=c.layerIds.concat(c.graphicsLayerIds),g,q,p=f.length;for(q=0;q<p;q++)g=c.getLayer(f[q]),g.loaded&&this._onLayerAdd(g)}}catch(k){}},startup:function(){},destroy:function(){h.forEach(this._eventConnections,l.disconnect);u.destroy(this.listNode);
this.map=this.domNode=this._eventConnections=this.listNode=this._attributions=this._pendingDfds=this.itemNodes=this._activeLayers=this._lastItem=this._sharedLayers=null},_onLayerAdd:function(d){try{var a=this._attributions,b=d.id;if(!w.isDefined(a[b])&&d.showAttribution)if(d.hasAttributionData){var c=d.getAttributionData();this._pendingDfds[b]=1;a[b]=c;c.addBoth(n.partial(this._onAttributionLoad,this,d))}else a[b]=d.copyright||d.copyrightText||"",a[b]?(d.suspended||this._activeLayers.push(b),this._createNode(b)):
this._onLayerRemove(d)}catch(e){}},_onAttributionLoad:function(d,a,b){var c=d._attributions,e=d._pendingDfds,f=a.id;if(e&&e[f]){delete e[f];if(!b||b instanceof Error)b="";c[f]=b?d._createIndexByLevel(b,-1!==a.declaredClass.toLowerCase().indexOf("vetiledlayer")):a.copyright||a.copyrightText||"";c[f]?(a.suspended||d._activeLayers.push(f),d._createNode(f)):d._onLayerRemove(a)}},_onLayerRemove:function(d){try{var a=d.id,b=this.itemNodes,c,e=-1;this._onLayerSuspend(d);delete this._attributions[a];delete this._pendingDfds[a];
c=this._getGroupIndex(a);-1!==c&&(e=h.indexOf(this._sharedLayers[c],a),-1!==e&&(this._sharedLayers[c].splice(e,1),1>=this._sharedLayers[c].length&&this._sharedLayers.splice(c,1)));b[a]&&-1===e&&u.destroy(b[a]);delete b[a];this._updateLastItem()}catch(f){}},_onLayerSuspend:function(d){try{var a=d.id;if(this._attributions[a]){var b=h.indexOf(this._activeLayers,a),c=this.itemNodes[a];-1!==b&&this._activeLayers.splice(b,1);c&&this._toggleItem(c,!1,this._getGroupIndex(a))}}catch(e){}},_adjustFocus:function(){var d=
this.domNode.scrollWidth>this.domNode.clientWidth,a=k.contains(this.domNode,"esriAttributionOpen");t.set(this.domNode,"tabIndex",d||a?"0":"")},_onLayerResume:function(d){try{var a=d.id,b=this._attributions[a],c=this.itemNodes[a];if(b&&(-1===h.indexOf(this._activeLayers,a)&&this._activeLayers.push(a),c)){var e=n.isString(b)?b:this._getContributorsList(b,this.map.extent,this.map.getLevel());n.isString(b)||t.set(c,"innerHTML",e?e+this._getDelimiter():"");e&&this._toggleItem(c,!0,this._getGroupIndex(a))}}catch(f){}},
_onExtentChange:function(d,a,b,c){try{var e=this._activeLayers,f=this._attributions,g=this.itemNodes,q,p,k,h,l=e.length||0;for(h=0;h<l;h++)if(p=e[h],k=f[p],(q=g[p])&&!n.isString(k)){var m=this._getContributorsList(k,d,c?c.level:-1);t.set(q,"innerHTML",m?m+this._getDelimiter():"");this._toggleItem(q,!!m,-1)}}catch(r){}this._adjustCursorStyle()},_createNode:function(d){if(this.domNode){var a=this._checkShareInfo(d),b=a&&a.sharedWith,b=b&&this.itemNodes[b],c=this.map,e=this._attributions[d],e=n.isString(e)?
e:this._getContributorsList(e,c.extent,c.getLevel()),c=!!e&&!c.getLayer(d).suspended;b?(this.itemNodes[d]=b,this._toggleItem(b,c,a.index)):(d=this.itemNodes[d]=u.create("span",{"class":this.itemClass,innerHTML:e?e+this._getDelimiter():"",style:{display:c?"inline":"none"}},this.listNode),c&&this._setLastItem(d));this._adjustCursorStyle()}},_checkShareInfo:function(d){var a=this._attributions,b,c,e=-1,f=a[d],g;if(f&&n.isString(f)){for(c in a)if(b=a[c],c!==d&&b&&n.isString(b)&&b.length===f.length&&b.toLowerCase()===
f.toLowerCase()){g=c;break}a=this._sharedLayers;b=a.length;if(g){for(c=0;c<b;c++)if(f=a[c],-1!==h.indexOf(f,g)){e=c;f.push(d);break}-1===e&&(e=a.push([g,d])-1)}}return-1<e?{index:e,sharedWith:g}:null},_getGroupIndex:function(d){var a=this._sharedLayers,b,c=a.length,e=-1;for(b=0;b<c;b++)if(-1!==h.indexOf(a[b],d)){e=b;break}return e},_getDelimiter:function(){var d=this.itemDelimiter;return d?"\x3cspan class\x3d'"+this.delimiterClass+"'\x3e"+d+"\x3c/span\x3e":""},_toggleItem:function(d,a,b){if(-1<b&&
!a){b=this._sharedLayers[b];var c,e=b.length,f=this._activeLayers;for(c=0;c<e;c++)if(-1!==h.indexOf(f,b[c]))return}r.set(d,"display",a?"inline":"none");this._updateLastItem()},_updateLastItem:function(){var d=this.listNode.childNodes,a;a=d.length;var b;if(a)for(a-=1;0<=a;a--)if(b=d[a],"none"!==r.get(b,"display")){this._setLastItem(b);break}this._adjustCursorStyle()},_setLastItem:function(d){var a=this.itemClass,b=this.lastItemClass;this._lastItem&&k.replace(this._lastItem,a,b);d&&(k.replace(d,b,a),
this._lastItem=d)},_createIndexByLevel:function(d,a){var b=d.contributors,c,e,f,g,k=b?b.length:0,p,h,n=new B(4326),l={},m;for(g=0;g<k;g++){c=b[g];h=(e=c.coverageAreas)?e.length:0;for(p=0;p<h;p++){f=e[p];m=f.bbox;m={extent:C.geographicToWebMercator(new D(m[1],m[0],m[3],m[2],n)),attribution:c.attribution||"",zoomMin:f.zoomMin-(a&&f.zoomMin?1:0),zoomMax:f.zoomMax-(a&&f.zoomMax?1:0),score:w.isDefined(f.score)?f.score:100,objectId:g};for(f=m.zoomMin;f<=m.zoomMax;f++)l[f]=l[f]||[],l[f].push(m)}}return l},
_getContributorsList:function(d,a,b){var c="";if(a&&w.isDefined(b)&&-1<b){d=d[b];b=a.getCenter().normalize();for(var e=d?d.length:0,f=[],g={},c=0;c<e;c++)a=d[c],!g[a.objectId]&&a.extent.contains(b)&&(g[a.objectId]=1,f.push(a));f.sort(function(a,b){return b.score-a.score||a.objectId-b.objectId});e=f.length;for(c=0;c<e;c++)f[c]=f[c].attribution;c=f.join(", ")}return c},_adjustCursorStyle:function(){var d=v.position(this.listNode.parentNode,!0).h;k.contains(this.listNode.parentNode,"esriAttributionOpen")?
(k.remove(this.listNode.parentNode,"esriAttributionOpen"),d>v.position(this.listNode.parentNode,!0).h?(r.set(this.listNode.parentNode,"cursor","pointer"),k.add(this.listNode.parentNode,"esriAttributionOpen")):r.set(this.listNode.parentNode,"cursor","default")):(k.add(this.listNode.parentNode,"esriAttributionOpen"),d<v.position(this.listNode.parentNode,!0).h?r.set(this.listNode.parentNode,"cursor","pointer"):r.set(this.listNode.parentNode,"cursor","default"),k.remove(this.listNode.parentNode,"esriAttributionOpen"));
this._adjustFocus()}});y("extend-esri")&&n.setObject("dijit.Attribution",s,A);return s});