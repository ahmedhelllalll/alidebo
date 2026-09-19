import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture';

const url = process.argv[2] || 'http://127.0.0.1:8000/dev-login?lang=ar&view=list';
const outputPath = process.argv[3] || 'e:\\alidebo\\alidebo\\public\\screenshot_capture_test.png';
const width = parseInt(process.argv[4] || '1280', 10);
const height = parseInt(process.argv[5] || '900', 10);

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9225',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=${width},${height}`
]);

chrome.stderr.on('data', () => {});

async function run() {
  await new Promise(r => setTimeout(r, 1500));

  const newTargetRes = await fetch('http://127.0.0.1:9225/json/new?' + encodeURIComponent(url), { method: 'PUT' });
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

  await send('Emulation.setDeviceMetricsOverride', {
    width,
    height,
    deviceScaleFactor: 1,
    mobile: false
  });

  const scrollTo = process.argv[6] || null;

  // Wait for redirect and render
  await new Promise(r => setTimeout(r, 2500));

  const isDark = process.argv[7] === 'dark';
  if (isDark) {
    await send('Runtime.evaluate', { expression: `document.documentElement.classList.add('dark'); localStorage.setItem('theme', 'dark');` });
    await new Promise(r => setTimeout(r, 800));
  }

  if (scrollTo) {
    await send('Runtime.evaluate', {
      expression: `document.querySelector('${scrollTo}')?.scrollIntoView({ behavior: 'instant', block: 'center' })`
    });
    await new Promise(r => setTimeout(r, 500));
  }

  const screenshot = await send('Page.captureScreenshot', { format: 'png' });
  fs.writeFileSync(outputPath, Buffer.from(screenshot.data, 'base64'));

  console.log(`Saved screenshot to ${outputPath}`);

  try {
    await send('Target.closeTarget', { targetId: target.id });
  } catch (e) {}

  chrome.kill();
  process.exit(0);
}

run().catch(err => {
  console.error(err);
  try { chrome.kill(); } catch (e) {}
  process.exit(1);
});
