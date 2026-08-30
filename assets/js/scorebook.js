document.addEventListener('DOMContentLoaded',function(){
    var select=document.getElementById('score-subject'), form=document.getElementById('scorebook'),dirty=false;
    if(select) select.addEventListener('change',function(){document.getElementById('score-class').value=select.options[select.selectedIndex].dataset.class||'';});
    if(!form) return;
    var status=document.getElementById('score-status'), button=document.getElementById('score-save');
    function refresh(){
        var count=0;
        form.querySelectorAll('tr[data-learner]').forEach(function(row){
            var inputs=Array.from(row.querySelectorAll('input[data-column]'));
            var changed=inputs.some(function(i){return i.value!==i.dataset.initial;});
            row.classList.toggle('is-dirty',changed);if(changed)count++;
            var total=row.querySelector('output');if(total)total.textContent=inputs.every(function(i){return i.value===''&&i.dataset.initial==='';})?'—':inputs.reduce(function(sum,i){return sum+Number(i.value===''?i.dataset.initial:i.value);},0);
        });
        dirty=count>0;status.textContent=dirty?count+' learner records have unsaved changes':'No unsaved changes';
    }
    form.addEventListener('input',refresh);
    form.addEventListener('keydown',function(event){
        if(event.key==='Enter' && event.target.matches('input[data-column]')){
            event.preventDefault();var inputs=Array.from(form.querySelectorAll('tr:not([hidden]) input[data-column]:not(:disabled)'));var next=inputs[inputs.indexOf(event.target)+1];if(next){next.focus();next.select();}
        }
    });
    window.addEventListener('beforeunload',function(event){if(dirty){event.preventDefault();event.returnValue='';}});
    form.addEventListener('submit',async function(event){
        event.preventDefault();if(!form.reportValidity())return;
        var changes=Array.from(form.querySelectorAll('tr.is-dirty')).map(function(row){var data={learner:row.dataset.learner,version:row.dataset.version};row.querySelectorAll('[data-column]').forEach(function(i){data[i.dataset.column]=i.value;});return data;});
        if(!changes.length){status.textContent='There are no changes to save.';return;}
        button.disabled=true;status.textContent='Saving scores…';
        var payload=new FormData(form);payload.set('changes',JSON.stringify(changes));
        // Keep inputs stable while the request is pending so the acknowledgement cannot erase newer edits.
        var inputs=Array.from(form.querySelectorAll('[data-column]'));inputs.forEach(function(i){i.disabled=true;});
        try{
            var response=await fetch(form.action,{method:'POST',body:payload,credentials:'same-origin'});var result=await response.json();
            if(!response.ok || !result.ok)throw new Error(result.message||'Unable to save. Please retry.');
            var rows=new Map(result.rows.map(function(r){return [r.uname,r];}));
            form.querySelectorAll('tr[data-learner]').forEach(function(row){var saved=rows.get(row.dataset.learner);if(!saved)return;row.dataset.version=saved.version;row.querySelectorAll('[data-column]').forEach(function(i){i.value=saved.record?saved.record[i.dataset.column]:'';i.dataset.initial=i.value;});row.querySelector('[data-row-status]').textContent=saved.record?'Recorded':'Not recorded';});
            refresh();status.textContent=result.message;
        }catch(error){status.textContent=error.message+' Your entries are still on screen.';}
        finally{inputs.forEach(function(i){i.disabled=false;});button.disabled=false;}
    });
});
