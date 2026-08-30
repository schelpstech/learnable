document.addEventListener('DOMContentLoaded',function(){
    var select=document.getElementById('discount-fee');
    var search=document.getElementById('discount-fee-search');
    if(search)search.addEventListener('input',function(){
        var value=search.value.trim().toLowerCase();
        Array.from(select.options).forEach(function(option){option.hidden=!!option.value&&!option.textContent.toLowerCase().includes(value)&&!option.selected;});
    });
    select.addEventListener('change',function(){
        var option=select.options[select.selectedIndex];
        document.getElementById('discount-expected').value=option.dataset.current||'0';
        document.getElementById('discount-amount').value=option.dataset.current||'';
        document.getElementById('discount-amount').max=option.dataset.maximum||'';
    });
});
