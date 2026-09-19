import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\4b4c8006-42c2-4732-8608-d1e8bba47901\\scratch\\chrome-inspect-sec';

if (fs.existsSync(userDataDir)) {
  try { fs.rmSync(userDataDir, { recursive: true, force: true }); } catch (e) {}
}

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9271',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=1280,900`
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/ar/directory/test-business';
    const newTargetRes = await fetch('http://127.0.0.1:9271/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
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

    const result = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          if (!sec) return { error: 'no #products' };
          const elements = Array.from(sec.querySelectorAll('svg, button, nav, [class*="arrow"], [class*="nav"], span.rounded-full')).map(el => ({
            tag: el.tagName,
            className: el.className,
            parentTag: el.parentElement?.tagName,
            parentClass: el.parentElement?.className,
            text: el.textContent?.trim(),
            outerHTML: el.outerHTML
          }));
          return {
            secHTML: sec.outerHTML,
            elements
          };
        })()
      `,
      returnByValue: true
    });

    console.log('Elements inside #products:', JSON.stringify(result.result.value.elements, null, 2));

    ws.close();
    chrome.kill();
    process.exit(0);
  } catch (e) {
    console.error(e);
    chrome.kill();
    process.exit(1);
  }
}, 1000);
