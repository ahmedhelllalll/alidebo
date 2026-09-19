import { spawn } from 'child_process';

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--remote-debugging-port=9230',
  '--no-first-run',
  '--no-default-browser-check',
  '--user-data-dir=C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture'
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/dev-login?lang=ar&edit=16';
    const res = await fetch('http://127.0.0.1:9230/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
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
                clientWidth: el.clientWidth,
                scrollWidth: el.scrollWidth,
                offsetWidth: el.offsetWidth,
                direction: s.direction,
                textAlign: s.textAlign,
                paddingInlineStart: s.paddingInlineStart,
                paddingInlineEnd: s.paddingInlineEnd,
                parentWidth: el.parentElement.clientWidth,
                parentDisplay: window.getComputedStyle(el.parentElement).display,
                parentDirection: window.getComputedStyle(el.parentElement).direction,
                inputRect: el.getBoundingClientRect(),
                parentRect: el.parentElement.getBoundingClientRect()
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
