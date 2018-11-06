// All material copyright ESRI, All Rights Reserved, unless otherwise specified.
// See http://js.arcgis.com/3.17/esri/copyright.txt for details.
//>>built
var RTree=function(a){var c=3,s=6;isNaN(a)||(c=Math.floor(a/2),s=a);this.min_width=c;this.max_width=s;var p={x:0,y:0,w:0,h:0,id:"root",nodes:[]};(function(){var a={};return function(g){var b=0;g in a?b=a[g]++:a[g]=0;return g+"_"+b}})();var u=function(a,g,b){var f=[],q=[],d=[];if(!a||!RTree.Rectangle.overlap_rectangle(a,b))return d;a={x:a.x,y:a.y,w:a.w,h:a.h,target:g};q.push(b.nodes.length);f.push(b);do if(b=f.pop(),g=q.pop()-1,"target"in a)for(;0<=g;){var e=b.nodes[g];if(RTree.Rectangle.overlap_rectangle(a,
e))if(a.target&&"leaf"in e&&e.leaf===a.target||!a.target&&("leaf"in e||RTree.Rectangle.contains_rectangle(e,a))){"nodes"in e?(d=t(e,!0,[],e),b.nodes.splice(g,1)):d=b.nodes.splice(g,1);RTree.Rectangle.make_MBR(b.nodes,b);delete a.target;b.nodes.length<c&&(a.nodes=t(b,!0,[],b));break}else"nodes"in e&&(q.push(g),f.push(b),b=e,g=e.nodes.length);g-=1}else if("nodes"in a){b.nodes.splice(g+1,1);0<b.nodes.length&&RTree.Rectangle.make_MBR(b.nodes,b);for(g=0;g<a.nodes.length;g++)v(a.nodes[g],b);a.nodes.length=
0;0===f.length&&1>=b.nodes.length?(a.nodes=t(b,!0,a.nodes,b),b.nodes.length=0,f.push(b),q.push(1)):0<f.length&&b.nodes.length<c?(a.nodes=t(b,!0,a.nodes,b),b.nodes.length=0):delete a.nodes}else RTree.Rectangle.make_MBR(b.nodes,b);while(0<f.length);return d},t=function(a,g,b,f){var c=[];if(!RTree.Rectangle.overlap_rectangle(a,f))return b;c.push(f.nodes);do{f=c.pop();for(var d=f.length-1;0<=d;d--){var e=f[d];RTree.Rectangle.overlap_rectangle(a,e)&&("nodes"in e?c.push(e.nodes):"leaf"in e&&(g?b.push(e):
b.push(e.leaf)))}}while(0<c.length);return b},v=function(a,g){var b;if(0===g.nodes.length)g.x=a.x,g.y=a.y,g.w=a.w,g.h=a.h,g.nodes.push(a);else{var f=-1,q=[],d;q.push(g);var e=g.nodes;do{-1!==f&&(q.push(e[f]),e=e[f].nodes,f=-1);for(var k=e.length-1;0<=k;k--){var m=e[k];if("leaf"in m){f=-1;break}var l=RTree.Rectangle.squarified_ratio(m.w,m.h,m.nodes.length+1),h=Math.max(m.x+m.w,a.x+a.w)-Math.min(m.x,a.x),n=Math.max(m.y+m.h,a.y+a.h)-Math.min(m.y,a.y),m=RTree.Rectangle.squarified_ratio(h,n,m.nodes.length+
2);if(0>f||Math.abs(m-l)<d)d=Math.abs(m-l),f=k}}while(-1!==f);f=a;do{if(b&&"nodes"in b&&0===b.nodes.length){d=b;b=q.pop();for(e=0;e<b.nodes.length;e++)if(b.nodes[e]===d||0===b.nodes[e].nodes.length){b.nodes.splice(e,1);break}}else b=q.pop();if("leaf"in f||"nodes"in f||Array.isArray(f)){if(Array.isArray(f)){for(d=0;d<f.length;d++)RTree.Rectangle.expand_rectangle(b,f[d]);b.nodes=b.nodes.concat(f)}else RTree.Rectangle.expand_rectangle(b,f),b.nodes.push(f);if(b.nodes.length<=s)f={x:b.x,y:b.y,w:b.w,h:b.h};
else{d=f=b.nodes;e=d.length-1;k=0;l=d.length-1;m=0;n=h=void 0;for(h=d.length-2;0<=h;h--)n=d[h],n.x>d[k].x?k=h:n.x+n.w<d[e].x+d[e].w&&(e=h),n.y>d[m].y?m=h:n.y+n.h<d[l].y+d[l].h&&(l=h);h=Math.abs(d[e].x+d[e].w-d[k].x);n=Math.abs(d[l].y+d[l].h-d[m].y);h>n?e>k?(h=d.splice(e,1)[0],n=d.splice(k,1)[0]):(n=d.splice(k,1)[0],h=d.splice(e,1)[0]):l>m?(h=d.splice(l,1)[0],n=d.splice(m,1)[0]):(n=d.splice(m,1)[0],h=d.splice(l,1)[0]);for(d=[{x:h.x,y:h.y,w:h.w,h:h.h,nodes:[h]},{x:n.x,y:n.y,w:n.w,h:n.h,nodes:[n]}];0<
f.length;){for(var e=f,k=d[0],l=d[1],h=RTree.Rectangle.squarified_ratio(k.w,k.h,k.nodes.length+1),n=RTree.Rectangle.squarified_ratio(l.w,l.h,l.nodes.length+1),p=void 0,v=void 0,m=void 0,u=e.length-1;0<=u;u--){var r=e[u],t,w;t=Math.min(k.x,r.x);w=Math.min(k.y,r.y);t=Math.max(k.x+k.w,r.x+r.w)-t;w=Math.max(k.y+k.h,r.y+r.h)-w;w=Math.abs(RTree.Rectangle.squarified_ratio(t,w,k.nodes.length+2)-h);var x;x=Math.min(l.x,r.x);t=Math.min(l.y,r.y);x=Math.max(l.x+l.w,r.x+r.w)-x;r=Math.max(l.y+l.h,r.y+r.h)-t;r=
Math.abs(RTree.Rectangle.squarified_ratio(x,r,l.nodes.length+2)-n);if(!v||!p||Math.abs(r-w)<p)v=u,p=Math.abs(r-w),m=r<w?l:k}h=e.splice(v,1)[0];k.nodes.length+e.length+1<=c?(k.nodes.push(h),RTree.Rectangle.expand_rectangle(k,h)):l.nodes.length+e.length+1<=c?(l.nodes.push(h),RTree.Rectangle.expand_rectangle(l,h)):(m.nodes.push(h),RTree.Rectangle.expand_rectangle(m,h))}f=d;1>q.length&&(b.nodes.push(d[0]),q.push(b),f=d[1])}}else RTree.Rectangle.expand_rectangle(b,f),f={x:b.x,y:b.y,w:b.w,h:b.h}}while(0<
q.length)}};this.serialize=function(){return JSON.stringify(p)};this.deserialize=function(a,c){var b=c=c||p;b.nodes=a.nodes;b.x=a.x;b.y=a.y;b.w=a.w;b.h=a.h;return c};this.search=function(a,c){var b=[a,!!c,[],p];if(void 0===a)throw"Wrong number of arguments. RT.Search requires at least a bounding rectangle.";return t.apply(this,b)};this.remove=function(a,c){var b=Array.prototype.slice.call(arguments);1===b.length&&b.push(!1);b.push(p);if(!1===c){var f=0,q=[];do f=q.length,q=q.concat(u.apply(this,b));
while(f!==q.length);return q}return u.apply(this,b)};this.insert=function(a,c){if(2>arguments.length)throw"Wrong number of arguments. RT.Insert requires at least a bounding rectangle and an object.";v({x:a.x,y:a.y,w:a.w,h:a.h,leaf:c},p);return p}};
RTree.Rectangle=function(a,c,s,p){var u,t,v,y,g,b;a.x?(u=a.x,v=a.y,0!==a.w&&!a.w&&a.x2?(g=a.x2-a.x,b=a.y2-a.y):(g=a.w,b=a.h)):(u=a,v=c,g=s,b=p);t=u+g;y=v+b;this.x1=this.x=function(){return u};this.y1=this.y=function(){return v};this.x2=function(){return t};this.y2=function(){return y};this.w=function(){return g};this.h=function(){return b};this.toJSON=function(){return'{"x":'+u.toString()+', "y":'+v.toString()+', "w":'+g.toString()+', "h":'+b.toString()+"}"};this.overlap=function(a){return this.x()<
a.x2()&&this.x2()>a.x()&&this.y()<a.y2()&&this.y2()>a.y()};this.expand=function(a){var c=Math.min(this.x(),a.x()),d=Math.min(this.y(),a.y());g=Math.max(this.x2(),a.x2())-c;b=Math.max(this.y2(),a.y2())-d;u=c;v=d;return this};this.setRect=function(a,b,c,e){}};RTree.Rectangle.overlap_rectangle=function(a,c){return a.x<c.x+c.w&&a.x+a.w>c.x&&a.y<c.y+c.h&&a.y+a.h>c.y};RTree.Rectangle.contains_rectangle=function(a,c){return a.x+a.w<=c.x+c.w&&a.x>=c.x&&a.y+a.h<=c.y+c.h&&a.y>=c.y};
RTree.Rectangle.expand_rectangle=function(a,c){var s,p;s=a.x<c.x?a.x:c.x;p=a.y<c.y?a.y:c.y;a.w=a.x+a.w>c.x+c.w?a.x+a.w-s:c.x+c.w-s;a.h=a.y+a.h>c.y+c.h?a.y+a.h-p:c.y+c.h-p;a.x=s;a.y=p;return a};RTree.Rectangle.make_MBR=function(a,c){if(1>a.length)return{x:0,y:0,w:0,h:0};c?(c.x=a[0].x,c.y=a[0].y,c.w=a[0].w,c.h=a[0].h):c={x:a[0].x,y:a[0].y,w:a[0].w,h:a[0].h};for(var s=a.length-1;0<s;s--)RTree.Rectangle.expand_rectangle(c,a[s]);return c};
RTree.Rectangle.squarified_ratio=function(a,c,s){var p=(a+c)/2;a*=c;return a*s/(a/(p*p))};