import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\4b4c8006-42c2-4732-8608-d1e8bba47901\\scratch\\chrome-verify-subtitle-nav';

if (fs.existsSync(userDataDir)) {
  try { fs.rmSync(userDataDir, { recursive: true, force: true }); } catch (e) {}
}

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9280',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=1280,900`
]);

setTimeout(async () => {
  try {
    const artifactDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\938fe4a0-7806-4b94-a00c-4806f106a22a';

    // ── 1. TEST ARABIC WITH 1 PRODUCT ──
    console.log('1. Navigating to Arabic profile (test-business, 1 product)...');
    let res = await fetch('http://127.0.0.1:9280/json/new?http%3A%2F%2F127.0.0.1%3A8000%2Far%2Fdirectory%2Ftest-business', { method: 'PUT' });
    let target = await res.json();
    let ws = new WebSocket(target.webSocketDebuggerUrl);

    let id = 1;
    let pending = new Map();
    ws.onmessage = (msg) => {
      const data = JSON.parse(msg.data);
      if (data.id && pending.has(data.id)) {
        const { resolve } = pending.get(data.id);
        pending.delete(data.id);
        resolve(data.result);
      }
    };
    const send = (method, params = {}) => new Promise(r => {
      const curId = id++;
      pending.set(curId, { resolve: r });
      ws.send(JSON.stringify({ id: curId, method, params }));
    });
    await new Promise(r => ws.onopen = r);
    await new Promise(r => setTimeout(r, 2000));

    // Scroll to products
    await send('Runtime.evaluate', {
      expression: `document.querySelector('#products')?.scrollIntoView({ behavior: 'instant', block: 'start' });`
    });
    await new Promise(r => setTimeout(r, 500));

    const arabicInfo = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          const subtitle = sec?.querySelector('p')?.textContent?.trim();
          const svgs = Array.from(sec?.querySelectorAll('svg') || []);
          const arrowSvgs = svgs.filter(s => s.parentElement?.tagName === 'SPAN' || s.parentElement?.tagName === 'BUTTON');
          return {
            subtitle,
            totalSvgsInSec: svgs.length,
            arrowSvgsFound: arrowSvgs.length
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Arabic info (1 product):', arabicInfo.value);

    let shotAr = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync(`${artifactDir}\\arabic_single_product_no_nav.png`, Buffer.from(shotAr.data, 'base64'));
    console.log('Saved arabic_single_product_no_nav.png');
    ws.close();

    // ── 2. TEST ENGLISH WITH 1 PRODUCT ──
    console.log('2. Navigating to English profile (test-business, 1 product)...');
    res = await fetch('http://127.0.0.1:9280/json/new?http%3A%2F%2F127.0.0.1%3A8000%2Fen%2Fdirectory%2Ftest-business', { method: 'PUT' });
    target = await res.json();
    ws = new WebSocket(target.webSocketDebuggerUrl);
    pending = new Map();
    ws.onmessage = (msg) => {
      const data = JSON.parse(msg.data);
      if (data.id && pending.has(data.id)) {
        const { resolve } = pending.get(data.id);
        pending.delete(data.id);
        resolve(data.result);
      }
    };
    await new Promise(r => ws.onopen = r);
    await new Promise(r => setTimeout(r, 2000));

    await send('Runtime.evaluate', {
      expression: `document.querySelector('#products')?.scrollIntoView({ behavior: 'instant', block: 'start' });`
    });
    await new Promise(r => setTimeout(r, 500));

    const englishInfo = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          const subtitle = sec?.querySelector('p')?.textContent?.trim();
          const svgs = Array.from(sec?.querySelectorAll('svg') || []);
          const arrowSvgs = svgs.filter(s => s.parentElement?.tagName === 'SPAN' || s.parentElement?.tagName === 'BUTTON');
          return {
            subtitle,
            totalSvgsInSec: svgs.length,
            arrowSvgsFound: arrowSvgs.length
          };
        })()
      `,
      returnByValue: true
    });
    console.log('English info (1 product):', englishInfo.value);

    let shotEn = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync(`${artifactDir}\\english_single_product_no_nav.png`, Buffer.from(shotEn.data, 'base64'));
    console.log('Saved english_single_product_no_nav.png');
    ws.close();

    // ── 3. TEST VENDOR WITH MULTIPLE PRODUCTS (seeded-business-profile-6aa05c2ed90b5) ──
    console.log('3. Navigating to vendor with multiple products...');
    res = await fetch('http://127.0.0.1:9280/json/new?http%3A%2F%2F127.0.0.1%3A8000%2Fen%2Fdirectory%2Fseeded-business-profile-6aa05c2ed90b5', { method: 'PUT' });
    target = await res.json();
    ws = new WebSocket(target.webSocketDebuggerUrl);
    pending = new Map();
    ws.onmessage = (msg) => {
      const data = JSON.parse(msg.data);
      if (data.id && pending.has(data.id)) {
        const { resolve } = pending.get(data.id);
        pending.delete(data.id);
        resolve(data.result);
      }
    };
    await new Promise(r => ws.onopen = r);
    await new Promise(r => setTimeout(r, 2000));

    await send('Runtime.evaluate', {
      expression: `document.querySelector('#products')?.scrollIntoView({ behavior: 'instant', block: 'start' });`
    });
    await new Promise(r => setTimeout(r, 500));

    const multiInfo = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          const countPill = sec?.querySelector('.px-3\\.5')?.textContent?.trim();
          const navBtns = Array.from(sec?.querySelectorAll('button[aria-label="Previous"], button[aria-label="Next"]') || []);
          return {
            countPill,
            navButtonsCount: navBtns.length
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Multi product info (helal):', multiInfo.value);

    let shotMulti = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync(`${artifactDir}\\multi_products_with_nav.png`, Buffer.from(shotMulti.data, 'base64'));
    console.log('Saved multi_products_with_nav.png');
    ws.close();

    chrome.kill();
    process.exit(0);
  } catch (err) {
    console.error('Error:', err);
    chrome.kill();
    process.exit(1);
  }
}, 1000);
