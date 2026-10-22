'use strict';
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const source = fs.readFileSync(require('node:path').join(__dirname,'../assets/js/school-website.js'),'utf8');
function setup(length, reduced=false) {
    const timers=new Map(), nodes={}, listeners={}, docListeners={}; let next=1;
    function node() { return {textContent:'',hidden:true,style:{},attributes:{},setAttribute(k,v){this.attributes[k]=v;},addEventListener(k,v){this[k]=v;}}; }
    ['track','play','count','live','controls','previous','next'].forEach(k=>nodes[k]=node());
    const slides=Array.from({length},node);
    const root={querySelectorAll:()=>slides,querySelector:s=>nodes[s.match(/data-gallery-(\w+)/)[1]],classList:{add(){}},addEventListener:(k,v)=>{listeners[k]=v;},contains:target=>target==='inside'};
    const motion={matches:reduced,addEventListener:(k,v)=>{listeners.motion=v;}};
    const doc={hidden:false,querySelectorAll:()=>[root],addEventListener:(k,v)=>{docListeners[k]=v;}};
    vm.runInNewContext(source,{document:doc,window:{matchMedia:()=>motion},Array,clearTimeout:id=>timers.delete(id),setTimeout:(fn,ms)=>{const id=next++;timers.set(id,{fn,ms});return id;}});
    return {nodes,slides,listeners,docListeners,doc,timers,advance(){assert.equal(timers.size,1);const [id,timer]=Array.from(timers)[0];assert.equal(timer.ms,10000);timers.delete(id);timer.fn();}};
}
let h=setup(3);
assert.equal(h.nodes.count.textContent,'1 / 3'); h.advance(); assert.equal(h.nodes.count.textContent,'2 / 3');
h.advance(); h.advance(); assert.equal(h.nodes.count.textContent,'1 / 3');
console.log('PASS: the gallery advances every 10 seconds and loops');
h.nodes.previous.click(); assert.equal(h.nodes.count.textContent,'3 / 3');
h.nodes.next.click(); assert.equal(h.nodes.count.textContent,'1 / 3');
assert.equal(h.slides[0].attributes['aria-hidden'],'false'); assert.equal(h.slides[1].attributes['aria-hidden'],'true');
console.log('PASS: previous/next controls and accessible slide state stay in sync');
h.nodes.play.click(); assert.equal(h.timers.size,0); h.nodes.play.click(); assert.equal(h.timers.size,1);
h.listeners.mouseenter(); assert.equal(h.timers.size,0); h.listeners.mouseleave(); assert.equal(h.timers.size,1);
h.listeners.focusin(); assert.equal(h.timers.size,0); h.listeners.focusout({relatedTarget:null}); assert.equal(h.timers.size,1);
h.doc.hidden=true;h.docListeners.visibilitychange();assert.equal(h.timers.size,0);
h.doc.hidden=false;h.docListeners.visibilitychange();assert.equal(h.timers.size,1);
console.log('PASS: pause, hover, keyboard focus and hidden tabs stop automatic movement');
h=setup(2,true);assert.equal(h.timers.size,0);assert.equal(h.nodes.play.textContent,'Play slideshow');
h.nodes.next.click();assert.equal(h.nodes.count.textContent,'2 / 2');assert.equal(h.timers.size,0);
h=setup(1);assert.equal(h.timers.size,0);assert.equal(h.nodes.controls.hidden,true);
console.log('PASS: reduced-motion preferences and single-photo galleries avoid autoplay');
