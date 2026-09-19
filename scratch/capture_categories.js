import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-cat-verify';

async function capture() {
  const chrome = spawn(chromePath, [
    '--headless=new',
    '--remote-debugging-port=9226',
    '--no-first-run',
    '--no-default-browser-check',
    `--user-data-dir=${userDataDir}`,
    '--window-size=1280,900'
  ]);

  await new Promise(r => setTimeout(r, 1500));

  async function getWs(url) {
    const res = await fetch('http://127.0.0.1:9226/json/new?' + encodeURIComponent(url), { method: 'PUT' });
    const target = await res.json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise(r => ws.onopen = r);

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

    return { send, target };
  }

  // 1. Capture Form with Category Dropdown Open (Product 16 owned by User 1)
  {
    const { send, target } = await getWs('http://127.0.0.1:8000/dev-login?lang=ar&edit=16');
    await new Promise(r => setTimeout(r, 2500));

    // Scroll to #tour-category-field and click the button
    await send('Runtime.evaluate', {
      expression: `
        const field = document.querySelector('#tour-category-field');
        if (field) {
          field.scrollIntoView({ behavior: 'instant', block: 'center' });
          const btn = field.querySelector('button[x-ref="catDropdownBtn"]');
          if (btn) btn.click();
        }
      `
    });

    await new Promise(r => setTimeout(r, 600));

    const shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\screenshot_category_form_open.png', Buffer.from(shot.data, 'base64'));
    console.log('Captured screenshot_category_form_open.png');
    await send('Target.closeTarget', { targetId: target.id });
  }

  // 1b. Capture Form with Category Dropdown Open in English
  {
    const { send, target } = await getWs('http://127.0.0.1:8000/dev-login?lang=en&edit=16');
    await new Promise(r => setTimeout(r, 2500));

    await send('Runtime.evaluate', {
      expression: `
        const field = document.querySelector('#tour-category-field');
        if (field) {
          field.scrollIntoView({ behavior: 'instant', block: 'center' });
          const btn = field.querySelector('button[x-ref="catDropdownBtn"]');
          if (btn) btn.click();
        }
      `
    });

    await new Promise(r => setTimeout(r, 600));

    const shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\screenshot_category_form_open_en.png', Buffer.from(shot.data, 'base64'));
    console.log('Captured screenshot_category_form_open_en.png');
    await send('Target.closeTarget', { targetId: target.id });
  }

  // 2. Capture Products Listing with Category Filter Open
  {
    const { send, target } = await getWs('http://127.0.0.1:8000/dev-login?lang=ar&view=card');
    await new Promise(r => setTimeout(r, 2500));

    await send('Runtime.evaluate', {
      expression: `
        const filter = document.querySelector('#tour-category-filter');
        if (filter) {
          filter.scrollIntoView({ behavior: 'instant', block: 'center' });
          const btn = filter.querySelector('button[x-ref="catDropdownBtn"]');
          if (btn) btn.click();
        }
      `
    });

    await new Promise(r => setTimeout(r, 600));

    const shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\screenshot_category_filter_open.png', Buffer.from(shot.data, 'base64'));
    console.log('Captured screenshot_category_filter_open.png');
    await send('Target.closeTarget', { targetId: target.id });
  }

  chrome.kill();
  process.exit(0);
}

capture().catch(err => {
  console.error(err);
  process.exit(1);
});
