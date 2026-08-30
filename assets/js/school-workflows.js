document.addEventListener('DOMContentLoaded', function () {
    var confirmation=document.createElement('dialog');
    confirmation.className='workspace-confirm';
    confirmation.setAttribute('aria-labelledby','workspace-confirm-title');
    confirmation.innerHTML='<h2 id="workspace-confirm-title">Please confirm</h2><p></p><div class="workspace-actions"><button type="button" class="workspace-button secondary" data-cancel autofocus>Keep record</button><button type="button" class="workspace-button danger" data-proceed>Confirm</button></div>';
    document.body.appendChild(confirmation);
    var pendingForm=null,pendingSubmitter=null;
    confirmation.querySelector('[data-cancel]').addEventListener('click',function(){confirmation.close();pendingForm=null;});
    confirmation.addEventListener('cancel',function(){pendingForm=null;});
    confirmation.querySelector('[data-proceed]').addEventListener('click',function(){
        var form=pendingForm,submitter=pendingSubmitter;confirmation.close();pendingForm=null;
        if(form){form.dataset.confirmed='yes';form.requestSubmit(submitter);}
    });
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if(form.dataset.confirmed==='yes')return;
            event.preventDefault();pendingForm=form;pendingSubmitter=event.submitter;
            confirmation.querySelector('p').textContent=form.dataset.confirm;
            confirmation.showModal();
        });
    });
    document.querySelectorAll('[data-table-search]').forEach(function (input) {
        input.addEventListener('input', function () {
            var needle = input.value.trim().toLowerCase();
            document.querySelectorAll(input.dataset.tableSearch + ' tbody tr').forEach(function (row) {
                row.hidden = !row.textContent.toLowerCase().includes(needle);
            });
        });
    });
    document.querySelectorAll('[data-print]').forEach(function (button) { button.addEventListener('click', function () { window.print(); }); });
});
