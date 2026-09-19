import { spawn } from 'child_process';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture';

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9227',
  '--no-first-run',
  `--user-data-dir=${userDataDir}`,
]);

async function run() {
  await new Promise(r => setTimeout(r, 1500));
  const res = await fetch('http://127.0.0.1:9227/json/new?http://127.0.0.1:8000/en/directory/test-business', { method: 'PUT' });
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
  await new Promise(r => setTimeout(r, 2000));

  const code = `
    (() => {
      const sidebar = document.querySelector('.sticky-sidebar');
      let el = sidebar;
      const overflows = [];
      while (el && el !== document.documentElement) {
        const cs = window.getComputedStyle(el);
        overflows.push({
          tag: el.tagName,
          id: el.id,
          class: el.className,
          overflow: cs.overflow,
          overflowX: cs.overflowX,
          overflowY: cs.overflowY,
          position: cs.position
        });
        el = el.parentElement;
      }
      return overflows;
    })()
  `;

  const evalRes = await send('Runtime.evaluate', { expression: code, returnByValue: true });
  console.log(JSON.stringify(evalRes.result.value, null, 2));

  await send('Target.closeTarget', { targetId: target.id });
  chrome.kill();
  process.exit(0);
}

run().catch(e => { console.error(e); chrome.kill(); process.exit(1); });
