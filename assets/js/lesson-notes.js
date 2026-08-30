document.addEventListener('DOMContentLoaded',function(){
    var form=document.getElementById('lesson-note-form');if(!form)return;
    var content=document.getElementById('lesson-content'),type=document.getElementById('lesson-note-type'),preview=document.getElementById('lesson-preview'),changed=false,saving=false;
    function count(){var words=content.value.replace(/<[^>]*>/g,' ').trim().split(/\s+/).filter(Boolean).length;document.getElementById('lesson-word-count').textContent=words+' words · about '+Math.max(1,Math.ceil(words/180))+' min read';}
    function dirty(){changed=true;document.getElementById('lesson-save-status').textContent='Unsaved changes';count();}
    function switchType(){document.getElementById('lesson-written').hidden=type.value!=='text';document.getElementById('lesson-web').hidden=type.value!=='online';document.getElementById('lesson-weblink').required=type.value==='online';preview.hidden=true;}
    if(window.jQuery && jQuery.fn.summernote){
        jQuery(content).summernote({height:380,placeholder:'Start with what your learners should understand…',toolbar:[['style',['style']],['font',['bold','italic','underline','clear']],['para',['ul','ol','paragraph']],['insert',['table','link']],['history',['undo','redo']]],styleTags:['p','h2','h3','blockquote'],disableDragAndDrop:true,callbacks:{onChange:function(html){content.value=html;dirty();}}});
        jQuery(content).next('.note-editor').find('.note-editable').attr({'aria-label':'Lesson content','aria-multiline':'true'});
        changed=false;document.getElementById('lesson-save-status').textContent='';
    }
    form.addEventListener('input',dirty);type.addEventListener('change',function(){switchType();dirty();});
    document.getElementById('lesson-preview-button').addEventListener('click',function(){
        // Parse off-DOM, keep lesson structure only, and never insert user-supplied attributes into the preview.
        var parsed=new DOMParser().parseFromString(content.value,'text/html');
        var allowed=['P','BR','H2','H3','H4','STRONG','B','EM','I','U','S','UL','OL','LI','BLOCKQUOTE','TABLE','THEAD','TBODY','TR','TH','TD','SUB','SUP','SPAN','DIV','HR','PRE','CODE'];
        Array.from(parsed.body.querySelectorAll('*')).reverse().forEach(function(node){
            if(!allowed.includes(node.tagName)){if(['SCRIPT','STYLE','IFRAME','OBJECT','SVG','MATH','FORM'].includes(node.tagName))node.remove();else node.replaceWith(...node.childNodes);}
            else Array.from(node.attributes).forEach(function(a){node.removeAttribute(a.name);});
        });
        preview.replaceChildren();
        if(type.value==='text')preview.append(...Array.from(parsed.body.childNodes).map(function(n){return document.importNode(n,true);}));
        else preview.textContent=document.getElementById('lesson-weblink').value;
        preview.hidden=false;
    });
    form.addEventListener('submit',function(){saving=true;});
    window.addEventListener('beforeunload',function(e){if(changed&&!saving){e.preventDefault();e.returnValue='';}});
    switchType();count();
});
