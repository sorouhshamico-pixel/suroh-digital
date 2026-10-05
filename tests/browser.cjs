const {chromium}=require('playwright');
const {execFileSync}=require('node:child_process');
const fs=require('node:fs');
const base=process.env.TEST_BASE_URL||'http://127.0.0.1:8081';
if(!/^http:\/\/127\.0\.0\.1:\d+$/.test(base))throw new Error('Loopback test URL required');
(async()=>{
 const credentials=JSON.parse(execFileSync('php',['tests/browser_seed.php'],{encoding:'utf8'}));
 const browser=await chromium.launch({executablePath:process.env.CHROME_PATH||'C:/Program Files/Google/Chrome/Application/chrome.exe',headless:true});
 const errors=[],consoleErrors=[],networkErrors=[],httpErrors=[];
 fs.mkdirSync('tests/artifacts',{recursive:true});
 let checks=0;
 const check=(ok,label)=>{if(!ok)throw new Error(label);checks++;console.log('PASS '+label);};
 const context=await browser.newContext({reducedMotion:'reduce'});
 const page=await context.newPage();
 page.on('pageerror',error=>errors.push(error.message));
 page.on('console',msg=>{if(msg.type()==='error')consoleErrors.push(msg.text());});
 page.on('requestfailed',req=>networkErrors.push({url:req.url().split('?')[0],error:req.failure()?.errorText}));
 page.on('response',res=>{if(res.status()>=400)httpErrors.push({url:res.url().split('?')[0],status:res.status()});});
 for(const width of [320,375,768,1440]){
  await page.setViewportSize({width,height:900});
  for(const path of ['/','/services/seo','/services/custom-software','/blog','/privacy','/terms','/admin/login']){
   const response=await page.goto(base+path,{waitUntil:'networkidle'});
   check(response.status()===200,width+' HTTP '+path);
   check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),width+' no overflow '+path);
   check(await page.locator('html').getAttribute('dir')==='rtl',width+' RTL '+path);
  }
  await page.goto(base+'/',{waitUntil:'networkidle'});
  check(await page.locator('.hero-photo img').evaluate(image=>image.complete&&image.naturalWidth===960),width+' local hero image loaded');
  check(await page.locator('[data-lucide] svg,svg.lucide').count()>0,width+' local icons loaded');
  check(await page.evaluate(()=>document.fonts.check('400 16px Tajawal')),width+' local Arabic font loaded');
  await page.screenshot({path:'tests/artifacts/home-'+width+'.png',fullPage:true});
  if(width<=768){
   await page.locator('[data-menu]').click();
   check(await page.locator('[data-menu]').getAttribute('aria-expanded')==='true',width+' mobile menu opens');
   await page.keyboard.press('Escape');check(await page.locator('[data-menu]').getAttribute('aria-expanded')==='false',width+' menu closes on Escape');
  }
 }
 await page.setViewportSize({width:375,height:900});
 await page.goto(base+'/?utm_source=mourjan&utm_medium=classified&utm_campaign=ui_qa&utm_content=ad_02&utm_term=seo',{waitUntil:'networkidle'});
 const visitor=await context.cookies();check(visitor.some(c=>c.name==='sd_visitor'&&c.httpOnly),'persistent first-party visitor cookie');
 await page.goto(base+'/services/seo',{waitUntil:'networkidle'});
 const attribution=await page.locator('#sd-context').textContent();
 check(JSON.parse(attribution).attribution.utm_source==='mourjan','browser UTM survives navigation');
 await page.goto(base+'/',{waitUntil:'networkidle'});
 await page.locator('[name=name]').fill('عميل اختبار المتصفح');
 await page.locator('[name=phone]').fill('0557654321');
 await page.locator('[name=message]').fill('اختبار إرسال من الجوال');
 await page.locator('[name=consent]').check();
 await page.waitForTimeout(2100);
 await Promise.all([page.waitForURL(url=>url.hash==='#contact'),page.locator('[data-lead-form] button[type=submit]').click()]);
 check(await page.locator('.alert.success').count()===1,'browser contact persisted and confirmed');
 const first=JSON.parse(await page.locator('#sd-context').textContent());check(first.conversion===true,'browser confirmed conversion');
 // Tracking failures must never cancel a phone/WhatsApp navigation action.
 await page.route('**/api/track',route=>route.abort());
 const action=await page.locator('a[data-event=whatsapp_click]').first().evaluate(element=>{
  let cancelled=false;const handler=event=>{cancelled=event.defaultPrevented;event.preventDefault();};
  document.addEventListener('click',handler,{once:true});
  element.click();return {cancelled,href:element.href};
 });
 check(!action.cancelled&&action.href.startsWith('https://wa.me/'),'WhatsApp unaffected by tracking failure');
 const phone=await page.locator('a[data-event=phone_click]').first().evaluate(element=>{
  let cancelled=false;document.addEventListener('click',event=>{cancelled=event.defaultPrevented;event.preventDefault();},{once:true});element.click();return {cancelled,href:element.href};
 });
 check(!phone.cancelled&&phone.href.startsWith('tel:'),'phone unaffected by tracking failure');
 await page.unroute('**/api/track');
 await page.goto(base+'/admin/login',{waitUntil:'networkidle'});
 await page.locator('[name=email]').fill(credentials.email);await page.locator('[name=password]').fill(credentials.password);
 await Promise.all([page.waitForURL(base+'/admin'),page.locator('.login-form button').click()]);
 for(const path of ['/admin','/admin/posts','/admin/posts/create','/admin/leads','/admin/analytics']){
  for(const width of [375,1440]){
   await page.setViewportSize({width,height:900});const response=await page.goto(base+path,{waitUntil:'networkidle'});
   check(response.status()===200,width+' admin '+path);
   check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),width+' admin no overflow '+path);
  }
 }
 await page.setViewportSize({width:375,height:900});await page.goto(base+'/admin/leads',{waitUntil:'networkidle'});
 await page.locator('[data-status-select]').first().selectOption('interested');await page.waitForLoadState('networkidle');
 check(await page.locator('[data-status-select]').first().inputValue()==='interested','CRM selector works under CSP');
 await page.screenshot({path:'tests/artifacts/admin-mobile.png',fullPage:true});
 check(errors.length===0,'no JavaScript page errors');
 check(httpErrors.length===0,'no unexpected HTTP resource errors');
 check(networkErrors.every(error=>error.url===base+'/api/track'),'only deliberate tracking outage errors');
 check(consoleErrors.every(error=>error.includes('net::ERR_FAILED')),'no unexpected console errors');
 fs.writeFileSync('tests/artifacts/browser-report.json',JSON.stringify({checks,errors,consoleErrors,networkErrors,httpErrors},null,2));
 console.log('ALL '+checks+' BROWSER CHECKS PASSED');
 console.log('Console errors: '+consoleErrors.length+'; network failures: '+networkErrors.length+' (see private artifact report)');
 await browser.close();
})().catch(error=>{console.error(error.message);process.exit(1);});
