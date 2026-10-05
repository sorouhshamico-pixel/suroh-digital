document.addEventListener('DOMContentLoaded',()=>{
  if(window.lucide)window.lucide.createIcons();
  const header=document.querySelector('[data-header]');
  const onScroll=()=>header?.classList.toggle('scrolled',window.scrollY>20);
  onScroll();addEventListener('scroll',onScroll,{passive:true});
  const menu=document.querySelector('[data-menu]'),nav=document.querySelector('[data-nav]');
  const close=()=>{nav?.classList.remove('open');menu?.setAttribute('aria-expanded','false');};
  menu?.addEventListener('click',()=>{const open=nav?.classList.toggle('open');menu.setAttribute('aria-expanded',String(!!open));});
  nav?.querySelectorAll('a').forEach(a=>a.addEventListener('click',close));
  document.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
  const reveal=()=>document.querySelectorAll('.reveal').forEach(el=>el.classList.add('is-visible'));
  if('IntersectionObserver' in window&&!matchMedia('(prefers-reduced-motion: reduce)').matches){
    const io=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-visible');io.unobserve(e.target);}}),{threshold:.1});
    document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
  }else reveal();
  let context={};try{context=JSON.parse(document.getElementById('sd-context')?.textContent||'{}');}catch(e){}
  const attribution=context.attribution||{};
  Object.keys(attribution).forEach(key=>document.querySelectorAll('[name="'+key+'"]').forEach(input=>input.value=attribution[key]||''));
  const track=(event)=>{
    // Navigation remains independent of analytics availability.
    const body=JSON.stringify({event,page:location.pathname,_token:context.token});
    try{
      const sent=navigator.sendBeacon&&navigator.sendBeacon('/api/track',new Blob([body],{type:'application/json'}));
      if(!sent)fetch('/api/track',{method:'POST',headers:{'Content-Type':'application/json'},body,keepalive:true}).catch(()=>{});
    }catch(e){}
    if(typeof window.gtag==='function')window.gtag('event',event);
    if(Array.isArray(window.dataLayer))window.dataLayer.push({event});
  };
  if(!location.pathname.startsWith('/admin')){
    track('page_view');if(location.pathname.startsWith('/services/'))track('service_view');
    document.querySelectorAll('a.track,a.btn').forEach(el=>el.addEventListener('click',()=>track(el.dataset.event||'cta_click')));
    const form=document.querySelector('[data-lead-form]');
    if(form){let started=false;form.addEventListener('focusin',()=>{if(!started){started=true;track('form_start');}});}
    // First-party form_submit is already committed on the server; external tags receive confirmed conversions only.
    if(context.conversion){if(typeof window.gtag==='function')window.gtag('event','form_submit');if(Array.isArray(window.dataLayer))window.dataLayer.push({event:'form_submit'});}
  }
});
