import { spawn } from 'child_process';
import fs from 'fs';

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--remote-debugging-port=9232',
  '--no-first-run',
  '--no-default-browser-check',
  '--user-data-dir=C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture'
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/dev-login?lang=ar&edit=16';
    const res = await fetch('http://127.0.0.1:9232/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
    const target = await res.json();
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
    await new Promise(r => setTimeout(r, 2500));

    const clipRes = await send('Runtime.evaluate', {
      expression: `(() => {
        const el = document.getElementById('tour-base-price-field');
        const r = el.getBoundingClientRect();
        return { x: r.x, y: r.y, width: r.width, height: r.height, scale: 1 };
      })()`,
      returnByValue: true
    });

    const clip = clipRes.result.value;
    console.log('Clip rect:', clip);

    const screenshot = await send('Page.captureScreenshot', { format: 'png', clip });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\screenshot_price_field_crop.png', Buffer.from(screenshot.data, 'base64'));
    console.log('Saved screenshot_price_field_crop.png');

    chrome.kill();
    process.exit(0);
  } catch (e) {
    console.error(e);
    chrome.kill();
    process.exit(1);
  }
}, 1500);
