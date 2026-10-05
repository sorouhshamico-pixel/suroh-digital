const {chromium}=require('playwright');
const {spawn,execFileSync}=require('node:child_process');
const fs=require('node:fs');
const path=require('node:path');
const base=process.env.TEST_BASE_URL||'http://127.0.0.1:8081';
if(!/^http:\/\/127\.0\.0\.1:\d+$/.test(base)||!process.env.DB_DATABASE?.endsWith('_test')||!process.env.STORAGE_PATH?.replaceAll('\\','/').includes('/storage/testing/'))throw new Error('Disposable loopback database/runtime required');
const widths=[1440,1280,1024,768,430,390];
const artifacts='tests/artifacts';
let checks=0;
const check=(ok,label)=>{if(!ok)throw new Error(label);checks++;};
const servers=[];
const reports=[];
function startServer(port,appUrl,production=false){
 const runtime=path.resolve('storage/testing/frontend-'+port);
 for(const folder of ['logs','sessions','cache'])fs.mkdirSync(path.join(runtime,folder),{recursive:true});
 const child=spawn('php',['-S','127.0.0.1:'+port,'-t','public','public/index.php'],{env:{...process.env,APP_URL:appUrl,APP_ENV:production?'production':'local',STORAGE_PATH:runtime},stdio:'ignore',windowsHide:true});
 servers.push(child);return 'http://127.0.0.1:'+port;
}
async function ready(request,url){for(let i=0;i<40;i++){try{const r=await request.get(url,{timeout:1000});if(r.status()===200)return;}catch{}await new Promise(resolve=>setTimeout(resolve,100));}throw new Error('Server unavailable: '+url);}
function assetsIn(directory){return fs.readdirSync(directory,{withFileTypes:true}).flatMap(entry=>entry.isDirectory()?assetsIn(path.join(directory,entry.name)):[path.join(directory,entry.name)]).filter(file=>/\.(css|js|png|webp|woff2)$/.test(file));}
(async()=>{
 fs.mkdirSync(artifacts,{recursive:true});
 const credentials=JSON.parse(execFileSync('php',['tests/browser_seed.php'],{encoding:'utf8'}));
 execFileSync('php',['tests/frontend_seed.php'],{stdio:'pipe'});
 const browser=await chromium.launch({executablePath:process.env.CHROME_PATH||'C:/Program Files/Google/Chrome/Application/chrome.exe',headless:true});
 try{
 const mounted=startServer(8087,'http://127.0.0.1:8087/sorouh');
 const production=startServer(8088,'https://digital.suroohalshami.com',true);
 const modes=[{name:'localhost',url:base,backend:base,prefix:''},{name:'subfolder',url:mounted+'/sorouh',backend:mounted,prefix:'/sorouh'},{name:'production-simulation',url:'https://digital.suroohalshami.com',backend:production,prefix:''}];
 for(const mode of modes){
  const context=await browser.newContext({reducedMotion:'reduce'});
  await ready(context.request,mode.backend+mode.prefix+'/');
  if(mode.name==='production-simulation')await context.route('https://digital.suroohalshami.com/**',async route=>{
   const target=new URL(route.request().url());
   const response=await route.fetch({url:mode.backend+target.pathname+target.search});
   await route.fulfill({response});
  });
  const errors=[],httpErrors=[],networkErrors=[],consoleErrors=[],responses=[];
  const page=await context.newPage();
  page.on('pageerror',e=>errors.push(e.message));
  page.on('console',m=>{if(m.type()==='error')consoleErrors.push(m.text());});
  page.on('requestfailed',r=>networkErrors.push({url:r.url(),failure:r.failure()?.errorText}));
  page.on('response',r=>{responses.push({url:r.url(),status:r.status(),type:r.headers()['content-type']});if(r.status()>=400)httpErrors.push({url:r.url(),status:r.status()});});
  for(const file of assetsIn('public/assets')){
   const assetPath='/'+file.replaceAll('\\','/').replace(/^public\//,'');
   const response=await context.request.get(mode.backend+mode.prefix+assetPath);
   check(response.status()===200,mode.name+' asset 200 '+assetPath);
   const type=response.headers()['content-type']||'';
   const expected={css:'text/css',js:'javascript',woff2:'font/woff2',webp:'image/webp',png:'image/png'}[path.extname(file).slice(1)];
   check(type.includes(expected),mode.name+' asset MIME '+assetPath);
   check((await response.body()).length>0,mode.name+' nonempty asset '+assetPath);
  }
  for(const width of widths){
   await page.setViewportSize({width,height:960});
   const response=await page.goto(mode.url+'/?utm_source=frontend_qa&utm_campaign=layout',{waitUntil:'networkidle'});
   check(response.status()===200,mode.name+' '+width+' homepage 200');
   await page.evaluate(async()=>{for(let y=0;y<document.documentElement.scrollHeight;y+=700){scrollTo(0,y);await new Promise(r=>setTimeout(r,35));}scrollTo(0,0);await document.fonts.ready;});
   await page.waitForTimeout(100);
   const metrics=await page.evaluate(()=>{
    const rect=el=>{const r=el.getBoundingClientRect();return {x:r.x,y:r.y,width:r.width,height:r.height,right:r.right,bottom:r.bottom};};
    const copy=rect(document.querySelector('.hero-copy')),visual=rect(document.querySelector('.hero-visual'));
    return {width:innerWidth,scrollWidth:document.documentElement.scrollWidth,lang:document.documentElement.lang,dir:document.documentElement.dir,
     background:getComputedStyle(document.body).backgroundColor,box:getComputedStyle(document.body).boxSizing,container:rect(document.querySelector('.hero-grid')),
     heroColumns:getComputedStyle(document.querySelector('.hero-grid')).gridTemplateColumns,copy,visual,
     images:[...document.images].map(i=>({src:i.getAttribute('src'),loaded:i.complete&&i.naturalWidth>0,rect:rect(i),parent:rect(i.parentElement)})),
     escaped:[...document.querySelectorAll('main *')].filter(el=>{const r=el.getBoundingClientRect();return r.width>0&&(r.left<-.5||r.right>innerWidth+.5);}).map(el=>el.tagName+'.'+el.className),
     canonical:document.querySelector('link[rel=canonical]').href,context:JSON.parse(document.querySelector('#sd-context').textContent),
     font:document.fonts.check('400 16px Tajawal'),icons:document.querySelectorAll('svg.lucide').length,
     formAction:document.querySelector('[data-lead-form]').getAttribute('action'),h1Count:document.querySelectorAll('h1').length,
     rawStyles:[...document.querySelectorAll('head link[rel=stylesheet]')].map(el=>el.getAttribute('href'))};
   });
   check(metrics.scrollWidth===width,mode.name+' '+width+' no horizontal scroll');
   check(metrics.escaped.length===0,mode.name+' '+width+' no offscreen content: '+metrics.escaped.join(','));
   check(metrics.lang==='ar'&&metrics.dir==='rtl',mode.name+' '+width+' Arabic RTL');
   check(metrics.background==='rgb(8, 10, 13)'&&metrics.box==='border-box',mode.name+' '+width+' design system/reset applied');
   check(metrics.container.width<=1360&&metrics.container.width<=width,mode.name+' '+width+' bounded container');
   check(metrics.images.every(i=>i.loaded),mode.name+' '+width+' all images loaded');
   check(metrics.images.every(i=>i.rect.width<=i.parent.width+1&&i.rect.width<=width),mode.name+' '+width+' bounded images');
   check(metrics.font&&metrics.icons>0,mode.name+' '+width+' local fonts/icons');
   check(metrics.h1Count===1,mode.name+' '+width+' semantic H1');
   check(!metrics.context.conversion,mode.name+' '+width+' no unconfirmed conversion');
   check(metrics.context.trackUrl===mode.prefix+'/api/track',mode.name+' '+width+' mounted tracking endpoint');
   check(metrics.formAction===mode.prefix+'/contact',mode.name+' '+width+' mounted contact action');
   check(metrics.rawStyles.length===2&&metrics.rawStyles.every(url=>url.startsWith(mode.prefix+'/assets/')&&url.includes('?v=')),mode.name+' '+width+' blocking versioned head styles');
   if(width>=1024)check(metrics.copy.x>metrics.visual.x&&Math.abs(metrics.copy.y-metrics.visual.y)<200,mode.name+' '+width+' desktop text right/image left');
   else check(metrics.copy.bottom<=metrics.visual.y+1,mode.name+' '+width+' mobile copy before image');
   check(metrics.canonical===mode.url+'/',mode.name+' '+width+' correct canonical');
   if(width<=768){await page.locator('[data-menu]').click();check(await page.locator('[data-menu]').getAttribute('aria-expanded')==='true',mode.name+' '+width+' menu opens');await page.keyboard.press('Escape');check(await page.locator('[data-menu]').getAttribute('aria-expanded')==='false',mode.name+' '+width+' menu closes');}
   await page.evaluate(()=>document.activeElement?.blur());
   if(mode.name==='localhost'){
    await page.screenshot({path:artifacts+'/frontend-home-'+width+'.png',fullPage:true});
    await page.screenshot({path:artifacts+'/frontend-hero-'+width+'.png'});
   }
   reports.push({mode:mode.name,width,metrics});
  }
  for(const route of ['/services/web-development','/services/digital-marketing','/services/seo','/services/graphic-design','/services/classified-ads','/services/custom-software','/blog','/blog/frontend-qa-article','/privacy','/terms','/admin/login']){
   const response=await page.goto(mode.url+route,{waitUntil:'networkidle'});
   check(response.status()===200,mode.name+' route '+route);
   check(await page.evaluate(()=>document.documentElement.scrollWidth===innerWidth),mode.name+' route no overflow '+route);
   check(await page.locator('link[rel=canonical]').getAttribute('href')===mode.url+route,mode.name+' route canonical '+route);
  }
  await page.goto(mode.url+'/blog/frontend-qa-article',{waitUntil:'networkidle'});
  await page.locator('.article-content').scrollIntoViewIfNeeded();await page.waitForTimeout(150);
  check(await page.locator('.article-content img').getAttribute('src')===mode.prefix+'/assets/img/development.webp',mode.name+' stored article media respects mount');
  check(await page.locator('.article-content a').getAttribute('href')===mode.prefix+'/services/seo',mode.name+' stored article links respect mount');
  check(await page.locator('.article-content img').evaluate(i=>i.complete&&i.naturalWidth>0&&i.getBoundingClientRect().width<=innerWidth),mode.name+' article image loads and fits');
  check(await page.evaluate(()=>document.documentElement.scrollWidth===innerWidth),mode.name+' wide article content stays bounded');
  if(mode.name==='subfolder'){
   await page.goto(mode.url+'/?utm_source=haraj&utm_campaign=mounted_contact',{waitUntil:'networkidle'});
   await page.goto(mode.url+'/services/seo',{waitUntil:'networkidle'});
   let data=JSON.parse(await page.locator('#sd-context').textContent());
   check(data.attribution.utm_campaign==='mounted_contact'&&data.attribution.landing_page==='/sorouh/','subfolder UTM survives navigation');
   const redirect=await context.request.get(mode.url+'/services/custom-solutions',{maxRedirects:0});
   check(redirect.status()===301&&redirect.headers().location==='/sorouh/services/custom-software','subfolder legacy redirect');
   const trailing=await context.request.get(mode.url+'/blog/?utm_source=haraj',{maxRedirects:0});
   check(trailing.status()===301&&trailing.headers().location==='/sorouh/blog?utm_source=haraj','subfolder slash redirect preserves query');
   await page.goto(mode.url+'/',{waitUntil:'networkidle'});
   await page.locator('[name=name]').fill('Frontend QA');await page.locator('[name=phone]').fill('0557654321');await page.locator('[name=consent]').check();await page.waitForTimeout(2100);
   await Promise.all([page.waitForURL(url=>url.pathname==='/sorouh/'&&url.hash==='#contact'),page.locator('[data-lead-form] button[type=submit]').click()]);
   check(await page.locator('.alert.success').count()===1,'subfolder actual contact submission');
   const outside=await context.request.get(mode.backend+'/services/seo');check(outside.status()===404,'subfolder rejects unmounted routes');
   await page.goto(mode.url+'/admin/login',{waitUntil:'networkidle'});await page.locator('[name=email]').fill(credentials.email);await page.locator('[name=password]').fill(credentials.password);
   await Promise.all([page.waitForURL(mode.url+'/admin'),page.locator('.login-form button').click()]);
   for(const route of ['/admin','/admin/posts','/admin/posts/create','/admin/leads','/admin/analytics']){
    const r=await page.goto(mode.url+route,{waitUntil:'networkidle'});check(r.status()===200,'subfolder authenticated '+route);check(await page.evaluate(()=>document.documentElement.scrollWidth===innerWidth),'subfolder admin no overflow '+route);
   }
   check((await context.cookies()).filter(c=>['sd_session','sd_visitor'].includes(c.name)).every(c=>c.path==='/sorouh/'),'subfolder scoped cookies');
  }
  check(errors.length===0,mode.name+' no JavaScript exceptions');check(consoleErrors.length===0,mode.name+' no console errors');check(httpErrors.length===0,mode.name+' no unexpected HTTP errors');check(networkErrors.length===0,mode.name+' no failed network requests');
  const missing=await context.request.get(mode.backend+mode.prefix+'/assets/css/missing.css');check(missing.status()===404&&!((await missing.text()).includes('<html')),mode.name+' missing asset explicit 404');
  console.log('PASS '+mode.name+' six viewports, assets/MIME, routes, console and network');
  fs.writeFileSync(artifacts+'/frontend-network-'+mode.name+'.json',JSON.stringify({errors,consoleErrors,httpErrors,networkErrors,responses},null,2));
  await context.close();
 }
 // Ordinary motion and disabled JavaScript both keep the content usable.
 for(const javaScriptEnabled of [true,false]){
  const context=await browser.newContext({javaScriptEnabled,viewport:{width:390,height:960},reducedMotion:'no-preference'});const page=await context.newPage();await page.goto(base+'/',{waitUntil:'networkidle'});
  await page.evaluate(()=>scrollTo(0,document.documentElement.scrollHeight));await page.waitForTimeout(800);
  check(await page.locator('[data-lead-form]').evaluate(el=>getComputedStyle(el).opacity==='1'),'visible contact with JS '+javaScriptEnabled);
  check(await page.locator('.hero-copy').evaluate(el=>getComputedStyle(el).opacity==='1'),'visible hero with JS '+javaScriptEnabled);
  check(await page.evaluate(()=>document.documentElement.scrollWidth===innerWidth),'motion/no-JS no overflow '+javaScriptEnabled);
  await context.close();
 }
 fs.writeFileSync(artifacts+'/frontend-report.json',JSON.stringify({checks,widths,reports},null,2));console.log('ALL '+checks+' FRONTEND CHECKS PASSED');
 }finally{await browser.close();for(const child of servers)child.kill();}
})().catch(error=>{for(const child of servers)child.kill();console.error(error.stack);process.exit(1);});
