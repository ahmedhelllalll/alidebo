import { spawn } from 'child_process';

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--remote-debugging-port=9231',
  '--no-first-run',
  '--no-default-browser-check',
  '--user-data-dir=C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture'
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/dev-login?lang=ar&edit=16';
    const res = await fetch('http://127.0.0.1:9231/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
    const target = await res.json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);

    ws.onopen = () => {
      setTimeout(() => {
        ws.send(JSON.stringify({
          id: 1,
          method: 'Runtime.evaluate',
          params: {
            expression: `(() => {
              const el = document.getElementById('base_price');
              const s = window.getComputedStyle(el);
              return {
                value: el.value,
                valueLength: el.value.length,
                clientWidth: el.clientWidth,
                offsetWidth: el.offsetWidth,
                fontSize: s.fontSize,
                fontFamily: s.fontFamily,
                color: s.color,
                letterSpacing: s.letterSpacing,
                textIndent: s.textIndent,
                overflow: s.overflow,
                whiteSpace: s.whiteSpace,
                display: s.display,
                boxSizing: s.boxSizing
              };
            })()`,
            returnByValue: true
          }
        }));
      }, 2500);
    };

    ws.onmessage = (msg) => {
      const d = JSON.parse(msg.data);
      if (d.id === 1) {
        console.log(JSON.stringify(d.result, null, 2));
        chrome.kill();
        process.exit(0);
      }
    };
  } catch (e) {
    console.error(e);
    chrome.kill();
    process.exit(1);
  }
}, 1500);
