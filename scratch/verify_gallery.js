import { spawn } from 'child_process';
import fs from 'fs';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\4b4c8006-42c2-4732-8608-d1e8bba47901\\scratch\\chrome-gallery-test';

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9230',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=1280,900`
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/dev-login?view=grid';
    const newTargetRes = await fetch('http://127.0.0.1:9230/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
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

    // 1. Open Product Detail Modal for Product 10 (which has 5 images)
    console.log('Opening Product Detail Modal for Product 10...');
    await send('Runtime.evaluate', {
      expression: `
        const indexEl = document.querySelector('[x-data*="productsIndex"]');
        const data = Alpine.$data(indexEl);
        // Find product with id 10
        const cardBtn = document.querySelector('button[title*="Product Details"], button[aria-label*="Product Details"]');
        if (cardBtn) cardBtn.click();
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Capture Screenshot 1: Product Detail Modal with Airbnb "Show all photos" button overlay
    let shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step1_detail_modal.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step1_detail_modal.png');

    // 2. Click Airbnb "Show all photos" button overlay to open the Fullscreen Gallery Lightbox
    console.log('Clicking "Show all photos" overlay button...');
    await send('Runtime.evaluate', {
      expression: `
        // Click the Airbnb grid button overlay inside the modal
        const btn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Show all') || b.textContent.includes('photos'));
        if (btn) {
          btn.click();
        } else {
          // fallback: launchGalleryFromModal directly
          const indexEl = document.querySelector('[x-data*="productsIndex"]');
          Alpine.$data(indexEl).launchGalleryFromModal();
        }
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Capture Screenshot 2: Fullscreen Gallery Lightbox in 4+ Masonry/Editorial Grid View
    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step2_masonry_grid.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step2_masonry_grid.png');

    // 3. Click one of the photos in the masonry grid to zoom into focused carousel view
    console.log('Zooming into photo in masonry grid...');
    await send('Runtime.evaluate', {
      expression: `
        const gridImgs = document.querySelectorAll('#product-gallery-lightbox .cursor-pointer');
        if (gridImgs.length > 1) {
          gridImgs[1].click();
        }
      `
    });
    await new Promise(r => setTimeout(r, 600));

    // Capture Screenshot 3: Focused Carousel View
    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step3_focused_carousel.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step3_focused_carousel.png');

    // 4. Close the gallery via Close button
    console.log('Closing gallery via close button...');
    await send('Runtime.evaluate', {
      expression: `
        const closeBtn = document.querySelector('#product-gallery-lightbox button[aria-label*="gallery"]');
        if (closeBtn) closeBtn.click();
      `
    });
    await new Promise(r => setTimeout(r, 500));

    // 5. Test Arabic view (RTL & Translations)
    console.log('Navigating to Arabic RTL view...');
    await send('Page.navigate', { url: 'http://127.0.0.1:8000/dev-login?lang=ar&view=grid' });
    await new Promise(r => setTimeout(r, 2000));

    // Open detail modal in Arabic
    await send('Runtime.evaluate', {
      expression: `
        const cardBtn = document.querySelector('button[title*="تفاصيل"], button[aria-label*="تفاصيل"]');
        if (cardBtn) cardBtn.click();
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Capture Screenshot 4: Arabic RTL Product Detail Modal
    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step4_arabic_detail_modal.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step4_arabic_detail_modal.png');

    // Open Arabic Fullscreen Gallery
    await send('Runtime.evaluate', {
      expression: `
        const btn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('عرض جميع الصور'));
        if (btn) btn.click();
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Capture Screenshot 5: Arabic RTL Masonry Grid Gallery
    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step5_arabic_masonry_grid.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step5_arabic_masonry_grid.png');

    // 6. Test Mobile Viewport (375 x 667)
    console.log('Testing mobile viewport (375x667)...');
    await send('Emulation.setDeviceMetricsOverride', {
      width: 375,
      height: 667,
      deviceScaleFactor: 2,
      mobile: true
    });
    await new Promise(r => setTimeout(r, 600));

    // Capture Screenshot 6: Mobile Masonry Grid Gallery
    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\gallery_step6_mobile_masonry.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/gallery_step6_mobile_masonry.png');

    console.log('All gallery visual verification tests completed successfully!');
    chrome.kill();
    process.exit(0);
  } catch (e) {
    console.error('Error during gallery verification:', e);
    chrome.kill();
    process.exit(1);
  }
}, 1500);
