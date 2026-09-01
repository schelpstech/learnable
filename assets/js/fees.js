document.addEventListener('DOMContentLoaded',function(){
    var createForm=document.querySelector('[data-fee-create]');
    if(createForm){
        var mode=createForm.querySelector('[data-fee-mode]');
        var classField=createForm.querySelector('[data-fee-class]');
        var groupField=createForm.querySelector('[data-fee-group]');
        var setCreateMode=function(){
            if(!mode)return;
            var isClass=mode.value==='class';
            [classField,groupField].forEach(function(field,index){
                if(!field)return;
                var visible=index===0?isClass:!isClass;
                field.hidden=!visible;
                field.querySelectorAll('input,select').forEach(function(input){input.disabled=!visible;input.required=visible;});
            });
        };
        if(mode){mode.addEventListener('change',setCreateMode);setCreateMode();}
    }

    var form=document.querySelector('[data-fee-assignment]');
    if(!form)return;
    var termSelect=form.querySelector('[data-fee-term]');
    var audience=form.querySelector('[data-fee-audience]');
    var classField=form.querySelector('[data-assignment-class]');
    var classSelect=form.querySelector('[data-assignment-class-select]');
    var learnerField=form.querySelector('[data-assignment-learner]');
    var learnerSelect=form.querySelector('[data-assignment-learner-select]');
    var learnerHelp=form.querySelector('[data-learner-help]');
    var feeSelect=form.querySelector('[data-assignment-fee]');
    var feePreview=form.querySelector('[data-fee-preview]');
    var customField=form.querySelector('[data-custom-amount]');
    var customInput=customField?customField.querySelector('input'):null;
    var currentLearner=learnerSelect?learnerSelect.dataset.current:'';

    if(termSelect){
        termSelect.addEventListener('change',function(){
            var url=new URL(window.location.href);
            url.searchParams.set('route','fee-assignments');
            url.searchParams.set('term',termSelect.value);
            url.searchParams.delete('class_id');
            url.searchParams.delete('status');
            window.location.assign(url.toString());
        });
    }

    var setVisible=function(field,visible,required){
        if(!field)return;
        field.hidden=!visible;
        field.querySelectorAll('input,select').forEach(function(input){input.disabled=!visible;input.required=visible&&required;});
    };
    var filterFees=function(){
        if(!feeSelect)return;
        var target=audience.value;
        var classId=classSelect.value;
        Array.prototype.forEach.call(feeSelect.options,function(option){
            if(!option.value||option.value==='PreviousBalance'){option.disabled=false;return;}
            var unavailable=target==='school'&&option.dataset.group==='class';
            if(target!=='school'&&option.dataset.group==='class'&&classId&&option.dataset.class!==classId)unavailable=true;
            option.disabled=unavailable;
        });
        if(feeSelect.selectedOptions.length&&feeSelect.selectedOptions[0].disabled)feeSelect.value='';
        updateFeePreview();
    };
    var updateFeePreview=function(){
        if(!feeSelect)return;
        var option=feeSelect.selectedOptions[0];
        var previous=option&&option.value==='PreviousBalance';
        setVisible(customField,previous,true);
        if(!option||!option.value)feePreview.textContent='Select a fee to review its amount and availability.';
        else if(previous)feePreview.textContent='Enter the verified brought-forward balance for each selected learner.';
        else feePreview.textContent='Official amount: ₦'+Number(option.dataset.amount||0).toLocaleString()+(option.dataset.group==='class'?' · class-specific':' · reusable fee');
    };
    var loadLearners=async function(){
        if(!learnerSelect||audience.value!=='learner')return;
        learnerSelect.innerHTML='<option value="">Loading learners…</option>';
        learnerSelect.disabled=true;
        learnerHelp.textContent='Loading active learners…';
        if(!classSelect.value){learnerSelect.innerHTML='<option value="">Choose class first</option>';learnerHelp.textContent='Choose a class to load its active learners.';return;}
        try{
            var response=await fetch(form.dataset.learnersUrl+'?class_id='+encodeURIComponent(classSelect.value),{headers:{Accept:'application/json'},credentials:'same-origin'});
            var data=await response.json();
            if(!response.ok||!data.ok)throw new Error(data.message||'Unable to load learners.');
            learnerSelect.innerHTML='<option value="">Choose learner</option>';
            data.learners.forEach(function(learner){
                var option=document.createElement('option');option.value=learner.uname;option.textContent=learner.learner_name+' · '+learner.uname;
                if(currentLearner===learner.uname)option.selected=true;learnerSelect.appendChild(option);
            });
            learnerHelp.textContent=data.learners.length+' active learner'+(data.learners.length===1?'':'s')+' available.';
        }catch(error){learnerSelect.innerHTML='<option value="">Unable to load learners</option>';learnerHelp.textContent=error.message;}
        finally{learnerSelect.disabled=false;learnerSelect.required=true;currentLearner='';}
    };
    var setAudience=function(load){
        var needsClass=audience.value!=='school';
        setVisible(classField,needsClass,true);
        setVisible(learnerField,audience.value==='learner',true);
        filterFees();
        if(load&&audience.value==='learner')loadLearners();
    };
    audience.addEventListener('change',function(){setAudience(true);});
    classSelect.addEventListener('change',function(){filterFees();loadLearners();});
    feeSelect.addEventListener('change',updateFeePreview);
    setAudience(Boolean(classSelect.value));updateFeePreview();
});
