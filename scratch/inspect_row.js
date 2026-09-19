import { spawn } from 'child_process';

const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
  '--headless=new',
  '--remote-debugging-port=9227',
  '--no-first-run',
  '--no-default-browser-check',
  '--user-data-dir=C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\74933514-82a4-4b30-9b72-93ad2254f643\\scratch\\chrome-profile-capture'
]);

setTimeout(async () => {
  try {
    const res = await fetch('http://127.0.0.1:9227/json/new?http://127.0.0.1:8000/dev-login?lang=ar&view=list', { method: 'PUT' });
    const target = await res.json();
    const ws = new WebSocket(target.webSocketDebuggerUrl);
    
    ws.onopen = () => {
      setTimeout(() => {
        ws.send(JSON.stringify({
          id: 2,
          method: 'Runtime.evaluate',
          params: {
            expression: `(() => {
              const rows = Array.from(document.querySelectorAll('.group.rounded-2xl .grid'));
              if (rows.length === 0) return 'No rows found';
              const targetRow = rows[0];
              const children = Array.from(targetRow.children).map((c, i) => ({
                colIndex: i + 1,
                className: c.className,
                rect: c.getBoundingClientRect(),
                computedGridColumn: window.getComputedStyle(c).gridColumn,
                computedPosition: window.getComputedStyle(c).position,
                computedLeft: window.getComputedStyle(c).left,
                computedRight: window.getComputedStyle(c).right
              }));
              const rowStyle = window.getComputedStyle(targetRow);
              return {
                rowRect: targetRow.getBoundingClientRect(),
                rowDisplay: rowStyle.display,
                rowGridTemplateColumns: rowStyle.gridTemplateColumns,
                children
              };
            })()`,
            returnByValue: true
          }
        }));
      }, 2500);
    };

      ws.onmessage = (msg) => {
        const d = JSON.parse(msg.data);
        if (d.id === 2) {
          console.log(JSON.stringify(d, null, 2));
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
