document.querySelectorAll('[data-confirm]').forEach(form=>form.addEventListener('submit',event=>{if(!confirm(form.dataset.confirm))event.preventDefault();}));
document.querySelectorAll('[data-status-select]').forEach(select=>select.addEventListener('change',()=>select.form.requestSubmit()));
