/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/store/Cache",["../_base/lang","../when"],function(h,f){var k=function(e,d,g){g=g||{};return h.delegate(e,{query:function(a,c){var b=e.query(a,c);b.forEach(function(a){(!g.isLoaded||g.isLoaded(a))&&d.put(a)});return b},queryEngine:e.queryEngine||d.queryEngine,get:function(a,c){return f(d.get(a),function(b){return b||f(e.get(a,c),function(b){b&&d.put(b,{id:a});return b})})},add:function(a,c){return f(e.add(a,c),function(b){d.add(b&&"object"==typeof b?b:a,c);return b})},put:function(a,c){d.remove(c&&
c.id||this.getIdentity(a));return f(e.put(a,c),function(b){d.put(b&&"object"==typeof b?b:a,c);return b})},remove:function(a,c){return f(e.remove(a,c),function(b){return d.remove(a,c)})},evict:function(a){return d.remove(a)}})};h.setObject("dojo.store.Cache",k);return k});