(function(){
  // Minimal JS; avoid heavy scripts.
  document.addEventListener('submit', async function(e){
    const form = e.target.closest('form');
    if(!form || !form.querySelector('input[name="action"][value="energi_lite_lead"]')) return;
    e.preventDefault();
    const data = new FormData(form);
    const res = await fetch(form.action, { method: 'POST', body: data, credentials:'same-origin' });
    const json = await res.json();
    alert(json?.data?.message || 'נשלח');
    form.reset();
  });
})();