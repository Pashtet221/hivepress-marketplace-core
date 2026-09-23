import { createRequire } from 'module';
import fs from 'fs';
import path from 'path';

const args = Object.fromEntries(process.argv.slice(2).map(v => {
  const i=v.indexOf('='); return [v.slice(2,i), v.slice(i+1)];
}));
const runtime=args.runtime;
const require=createRequire(path.join(runtime,'package.json'));
const { chromium }=require('playwright');
const sharp=require('sharp');
process.env.PLAYWRIGHT_BROWSERS_PATH=path.join(runtime,'browsers');

const browser=await chromium.launch({headless:true,args:['--no-sandbox','--disable-dev-shm-usage']});
try {
  const width=Number(args.width||1440), height=Number(args.height||1200), maxHeight=Number(args['max-height']||3000), quality=Number(args.quality||78);
  const page=await browser.newPage({viewport:{width,height}});
  await page.goto(args.url,{waitUntil:'domcontentloaded',timeout:60000});
  await page.waitForTimeout(1500);
  await page.evaluate(async()=>{ for(let y=0;y<document.body.scrollHeight;y+=700){window.scrollTo(0,y); await new Promise(r=>setTimeout(r,80));} window.scrollTo(0,0); });
  const png=args.output+'.png';
  if(args.selector){ const el=page.locator(args.selector).first(); await el.waitFor({state:'visible',timeout:15000}); await el.screenshot({path:png}); }
  else await page.screenshot({path:png,fullPage:args['full-page']!=='0'});
  let image=sharp(png).resize({width:1600,withoutEnlargement:true});
  const meta=await sharp(png).metadata();
  if(maxHeight>0 && meta.height>maxHeight) image=image.extract({left:0,top:0,width:meta.width,height:maxHeight}).resize({width:1600,withoutEnlargement:true});
  await image.webp({quality}).toFile(args.output);
  fs.unlinkSync(png);
  console.log(JSON.stringify({ok:true,output:args.output}));
} finally { await browser.close(); }
