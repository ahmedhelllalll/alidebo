import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\4b4c8006-42c2-4732-8608-d1e8bba47901\\scratch\\chrome-gallery-test';

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9231',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=1280,900`
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/dev-login?view=grid';
    const newTargetRes = await fetch('http://127.0.0.1:9231/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
    const target = await newTargetRes.json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);

    let id = 1;
    const pending = new Map();

    ws.onmessage = (msg) => {
      const data = JSON.parse(msg.data);
      if (data.id && pending.has(data.id)) {
        const { resolve, reject } = pending.get(data.id);
        pending.delete(data.id);
        if (data.error) reject(data.error);
        else resolve(data.result);
      }
    };

    const send = (method, params = {}) => new Promise((resolve, reject) => {
      const curId = id++;
      pending.set(curId, { resolve, reject });
      ws.send(JSON.stringify({ id: curId, method, params }));
    });

    await new Promise(r => ws.onopen = r);
    await new Promise(r => setTimeout(r, 2000));

    // Open detail modal
    await send('Runtime.evaluate', {
      expression: `
        const indexEl = document.querySelector('[x-data*="productsIndex"]');
        const data = Alpine.$data(indexEl);
        const card = document.querySelector('[x-data*="productsIndex"] .group');
        const imgCover = card.querySelector('.aspect-\\\\[4\\\\/3\\\\]');
        if (imgCover) imgCover.click();
      `
    });
    await new Promise(r => setTimeout(r, 1000));

    // Inspect geometry and computed styles
    const geom = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const dialog = document.querySelector('[aria-labelledby="product-detail-title"]');
          const card = dialog ? dialog.querySelector('.max-w-3xl') : null;
          const airbnbBtn = Array.from(card ? card.querySelectorAll('button') : []).find(b => b.textContent.includes('Show all') || b.textContent.includes('photos'));
          
          return JSON.stringify({
            dialog: {
              rect: dialog ? dialog.getBoundingClientRect() : null,
              computedHeight: dialog ? window.getComputedStyle(dialog).height : null,
            },
            card: {
              rect: card ? card.getBoundingClientRect() : null,
              maxHeight: card ? window.getComputedStyle(card).maxHeight : null,
              height: card ? window.getComputedStyle(card).height : null,
              overflow: card ? window.getComputedStyle(card).overflow : null,
            },
            airbnbBtn: airbnbBtn ? {
              text: airbnbBtn.textContent.trim(),
              rect: airbnbBtn.getBoundingClientRect(),
              computed: {
                position: window.getComputedStyle(airbnbBtn).position,
                bottom: window.getComputedStyle(airbnbBtn).bottom,
                right: window.getComputedStyle(airbnbBtn).right,
                left: window.getComputedStyle(airbnbBtn).left,
                display: window.getComputedStyle(airbnbBtn).display,
                zIndex: window.getComputedStyle(airbnbBtn).zIndex,
              }
            } : 'BUTTON NOT FOUND'
          }, null, 2);
        })()
      `
    });

    console.log('Geometry:\n', geom.result ? geom.result.value : geom);

    // Capture screenshot of detail modal
    const shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\inspect_modal.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/inspect_modal.png');

    chrome.kill();
    process.exit(0);
  } catch (err) {
    console.error(err);
    chrome.kill();
    process.exit(1);
  }
}, 1500);
