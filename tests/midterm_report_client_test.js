'use strict';
const fs=require('node:fs'),vm=require('node:vm'),assert=require('node:assert/strict');
const source=fs.readFileSync(require('node:path').join(__dirname,'../assets/js/midterm-report.js'),'utf8');
function setup(count=1,broken=false,library=true) {
    const calls=[],canvases=[],buttons={download:{},print:{}},status={textContent:''};
    Object.values(buttons).forEach(b=>{b.addEventListener=(k,fn)=>{b[k]=fn;};});
    const sheets=Array.from({length:count},(_,id)=>({id,dataset:{},clientHeight:794,style:{setProperty(){}},querySelector:()=>({scrollHeight:650})}));
    const root={dataset:{midtermFile:'test-midterm.pdf'},hasAttribute:()=>false,querySelectorAll:s=>s==='img' ? [{complete:true,naturalWidth:broken ? 0 : 160}] : sheets};
    let pages=1,saved=0,printed=0;
    const pdf={internal:{getNumberOfPages:()=>pages},addPage:(format,orientation)=>{assert.equal(format,'a4');assert.equal(orientation,'landscape');pages++;},addImage:(...args)=>{calls.push(args);},save:name=>{assert.equal(name,'test-midterm.pdf');saved++;}};
    const html2pdf=()=>{
        let item;
        const worker={set:options=>{assert.equal(options.jsPDF.orientation,'landscape');return worker;},from:(node,type)=>{item=node;if(node.isCanvas)assert.equal(type,'canvas','the bundled library requires explicit canvas source mode');return worker;},toCanvas:()=>worker,toPdf:()=>worker,get:key=>Promise.resolve(key==='canvas' ? {width:2245,height:1600} : pdf)};
        return worker;
    };
    const context={Promise,Array,Date,Math,Error,document:{documentElement:{dataset:{pdfLoader:library?'ready':'error'}},querySelector:s=>s==='[data-midterm-reports]'?root:s==='[data-midterm-status]'?status:null,querySelectorAll:s=>s==='[data-midterm-print]'?[buttons.print]:s==='[data-midterm-download]'?[buttons.download]:Object.values(buttons),createElement:()=>{const c={isCanvas:true,getContext:()=>({fillRect(){},drawImage:(...args)=>{assert.equal(args.length,5,'the entire source canvas is scaled, never cropped');}}),toDataURL:()=> 'data:image/jpeg;base64,QA'};canvases.push(c);return c;}},window:{getComputedStyle:()=>({paddingTop:'30',paddingBottom:'30'}),addEventListener(){},print:()=>{printed++;},setTimeout,html2pdf:library?html2pdf:undefined}};
    vm.runInNewContext(source,context);
    return {buttons,root,status,canvases,saved:()=>saved,printed:()=>printed,pages:()=>pages,calls};
}
(async()=>{
    let h=setup(3);await h.buttons.download.click();assert.equal(h.saved(),1);assert.equal(h.pages(),3);assert.equal(h.root.dataset.exportPages,'3');assert.equal(h.root.dataset.exportStatus,'complete');assert.equal(h.canvases.length,3);assert(h.canvases.every(c=>c.height===Math.floor(c.width*210/297)));assert.equal(h.buttons.download.disabled,false);
    console.log('PASS: class export writes every sheet to one landscape PDF without dropping pages');
    h=setup(1);await h.buttons.download.click();assert.equal(h.saved(),1);assert.equal(h.pages(),1);
    console.log('PASS: individual reports export as one landscape page');
    h=setup(1,true);await h.buttons.download.click();assert.equal(h.saved(),0);assert.equal(h.root.dataset.exportStatus,'error');assert.match(h.status.textContent,/image could not/);assert.equal(h.buttons.print.disabled,false);
    console.log('PASS: broken images stop export with an actionable error and restore controls');
    h=setup(1,false,false);await h.buttons.download.click();assert.equal(h.saved(),0);assert.match(h.status.textContent,/Print \/ Save PDF/);assert.equal(h.buttons.download.disabled,false);
    console.log('PASS: missing PDF libraries leave the browser print fallback available');
    h=setup(1);await h.buttons.print.click();assert.equal(h.printed(),1);
    console.log('PASS: print waits for report images and prepares the same landscape sheets');
})().catch(e=>{console.error(e);process.exitCode=1;});
