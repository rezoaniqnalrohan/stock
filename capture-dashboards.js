const puppeteer = require('puppeteer');
const path = require('path');

const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

const servers = [
  { name: 'faststock', port: 8000, email: 'admin@faststock.test' },
  { name: 'medstock', port: 8001, email: 'admin@medstock.test' },
  { name: 'electrostock', port: 8002, email: 'admin@electrostock.test' },
  { name: 'freshstock', port: 8003, email: 'admin@freshstock.test' }
];

(async () => {
  const browser = await puppeteer.launch();

  for (const server of servers) {
    console.log(`Capturing ${server.name}...`);
    const page = await browser.newPage();
    page.setViewport({ width: 1280, height: 720 });

    try {
      // Navigate to server with longer timeout
      await page.goto(`http://localhost:${server.port}`, { waitUntil: 'domcontentloaded', timeout: 60000 });

      // Check if we're on login page
      const isLoginPage = await page.$('input[type="email"]') !== null;

      if (isLoginPage) {
        console.log(`  Logging in as ${server.email}...`);
        // Log in
        await page.type('input[type="email"]', server.email);
        await page.type('input[type="password"]', 'password');

        // Find and click the sign in button
        const buttons = await page.$$('button');
        if (buttons.length > 0) {
          await buttons[buttons.length - 1].click();
        }

        // Wait for page to load
        await delay(3000);
      }

      // Wait for dashboard to fully render
      await delay(2000);

      // Take screenshot
      const screenshotPath = path.join(
        __dirname,
        server.name,
        'screenshots',
        'dashboard.png'
      );

      await page.screenshot({ path: screenshotPath, fullPage: false });
      console.log(`✓ Saved: ${screenshotPath}`);
    } catch (error) {
      console.error(`✗ Error capturing ${server.name}:`, error.message);
    } finally {
      await page.close();
    }
  }

  await browser.close();
  console.log('\nAll dashboards captured!');
})();
