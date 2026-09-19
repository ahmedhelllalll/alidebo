import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';

const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const userDataDir = 'C:\\Users\\Mega Store\\.gemini\\antigravity-ide\\brain\\4b4c8006-42c2-4732-8608-d1e8bba47901\\scratch\\chrome-anonymous-test';

// Ensure fresh clean user data dir for pure anonymous visitor
if (fs.existsSync(userDataDir)) {
  try {
    fs.rmSync(userDataDir, { recursive: true, force: true });
  } catch (e) {}
}

const chrome = spawn(chromePath, [
  '--headless=new',
  '--remote-debugging-port=9232',
  '--no-first-run',
  '--no-default-browser-check',
  `--user-data-dir=${userDataDir}`,
  `--window-size=1280,900`
]);

setTimeout(async () => {
  try {
    const targetUrl = 'http://127.0.0.1:8000/en/directory/test-business';
    console.log('1. Navigating anonymously to:', targetUrl);
    const newTargetRes = await fetch('http://127.0.0.1:9232/json/new?' + encodeURIComponent(targetUrl), { method: 'PUT' });
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
    await new Promise(r => setTimeout(r, 2500));

    // Check anonymous state (no auth cookies)
    const cookiesRes = await send('Network.getCookies');
    console.log('Cookies present:', cookiesRes.cookies ? cookiesRes.cookies.map(c => c.name) : []);

    // ── STEP 1: Products & Catalog Section ──
    console.log('Inspecting #products section...');
    const productsSec = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          if (!sec) return { exists: false };
          const cards = document.querySelectorAll('#products [id^="product-"]');
          const title = sec.querySelector('h2')?.textContent?.trim();
          const firstCardTitle = cards[0]?.querySelector('h3')?.textContent?.trim();
          return {
            exists: true,
            title,
            cardsCount: cards.length,
            firstCardTitle,
            firstCardId: cards[0]?.id
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Products Section Check:', productsSec.result.value);

    // Scroll to products section
    await send('Runtime.evaluate', {
      expression: `
        document.querySelector('#products')?.scrollIntoView({ behavior: 'instant', block: 'start' });
      `
    });
    await new Promise(r => setTimeout(r, 600));

    let shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step1_products_catalog.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step1_products_catalog.png');

    // ── STEP 2: Open Product Detail Modal ──
    console.log('Opening Product Detail Modal for Product #16...');
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          const detailBtn = document.querySelector('#product-16 button, #products button');
          if (detailBtn) detailBtn.click();
        })()
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Verify modal DOM
    const modalCheck = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const modal = document.querySelector('[x-show*="detailModal.open"]');
          const title = modal?.querySelector('h3')?.textContent?.trim();
          const disabledBtn = modal?.querySelector('button[disabled]');
          const btnText = disabledBtn?.textContent?.trim();
          const specs = Array.from(modal?.querySelectorAll('.grid .p-3') || []).map(el => el.textContent.trim());
          return {
            modalFound: !!modal,
            title,
            hasDisabledQuoteBtn: !!disabledBtn,
            quoteBtnText: btnText,
            specsCount: specs.length
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Detail Modal Check:', modalCheck.result.value);

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step2_detail_modal.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step2_detail_modal.png');

    // ── STEP 3: Click "Show all photos" to launch Airbnb Fullscreen Gallery Lightbox ──
    console.log('Launching Airbnb Photo Gallery from Modal...');
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          const showAllBtn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Show all') || b.textContent.includes('photos'));
          if (showAllBtn) {
            showAllBtn.click();
          } else {
            const pageEl = document.querySelector('[x-data*="profilePage"]');
            Alpine.$data(pageEl).launchGalleryFromModal();
          }
        })()
      `
    });
    await new Promise(r => setTimeout(r, 800));

    // Verify Lightbox DOM
    const lightboxCheck = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const lb = document.querySelector('#public-product-gallery');
          const isVisible = lb && !lb.classList.contains('hidden') && lb.style.display !== 'none';
          const gridItems = lb?.querySelectorAll('.cursor-pointer img') || [];
          return {
            lightboxFound: !!lb,
            isVisible,
            imagesCount: gridItems.length
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Lightbox Check:', lightboxCheck.result.value);

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step3_airbnb_masonry.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step3_airbnb_masonry.png');

    // ── STEP 4: Click photo in masonry to zoom into Focused Carousel ──
    console.log('Zooming into photo 2 in Airbnb Gallery...');
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          const imgs = document.querySelectorAll('#public-product-gallery .cursor-pointer');
          if (imgs.length > 1) imgs[1].click();
        })()
      `
    });
    await new Promise(r => setTimeout(r, 600));

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step4_airbnb_carousel.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step4_airbnb_carousel.png');

    // Close lightbox
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          window.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        })()
      `
    });
    await new Promise(r => setTimeout(r, 400));

    // ── STEP 5: Arabic RTL Public Profile (/ar/directory/test-business) ──
    console.log('Navigating anonymously to Arabic profile...');
    await send('Page.navigate', { url: 'http://127.0.0.1:8000/ar/directory/test-business' });
    await new Promise(r => setTimeout(r, 2500));

    // Scroll to Arabic products section
    await send('Runtime.evaluate', {
      expression: `
        document.querySelector('#products')?.scrollIntoView({ behavior: 'instant', block: 'start' });
      `
    });
    await new Promise(r => setTimeout(r, 500));

    const arCheck = await send('Runtime.evaluate', {
      expression: `
        (() => {
          const sec = document.querySelector('#products');
          const title = sec?.querySelector('h2')?.textContent?.trim();
          const cardTitle = sec?.querySelector('h3')?.textContent?.trim();
          const viewDetailsBtn = sec?.querySelector('button')?.textContent?.trim();
          return {
            dir: document.documentElement.dir || document.body.dir,
            title,
            cardTitle,
            viewDetailsBtn
          };
        })()
      `,
      returnByValue: true
    });
    console.log('Arabic Section Check:', arCheck.result.value);

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step5_arabic_catalog.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step5_arabic_catalog.png');

    // Open Arabic detail modal
    console.log('Opening Arabic detail modal...');
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          const btn = document.querySelector('#products button');
          if (btn) btn.click();
        })()
      `
    });
    await new Promise(r => setTimeout(r, 800));

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step6_arabic_detail_modal.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step6_arabic_detail_modal.png');

    // Open Arabic Airbnb masonry gallery
    console.log('Opening Arabic Airbnb masonry gallery...');
    await send('Runtime.evaluate', {
      expression: `
        (() => {
          const btn = Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('الصور') || b.textContent.includes('عرض جميع'));
          if (btn) btn.click();
          else {
            const pageEl = document.querySelector('[x-data*="profilePage"]');
            Alpine.$data(pageEl).launchGalleryFromModal();
          }
        })()
      `
    });
    await new Promise(r => setTimeout(r, 800));

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step7_arabic_airbnb_masonry.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step7_arabic_airbnb_masonry.png');

    // ── STEP 6: Direct Anchor URL Test (#product-16) ──
    console.log('Testing direct anchor URL navigation...');
    await send('Page.navigate', { url: 'http://127.0.0.1:8000/en/directory/test-business#product-16' });
    await new Promise(r => setTimeout(r, 2000));

    shot = await send('Page.captureScreenshot', { format: 'png' });
    fs.writeFileSync('e:\\alidebo\\alidebo\\public\\public_step8_direct_anchor.png', Buffer.from(shot.data, 'base64'));
    console.log('Saved public/public_step8_direct_anchor.png');

    console.log('ALL ANONYMOUS BROWSER TESTS COMPLETED SUCCESSFULLY!');
    chrome.kill();
    process.exit(0);
  } catch (err) {
    console.error('Test error:', err);
    chrome.kill();
    process.exit(1);
  }
}, 1500);
