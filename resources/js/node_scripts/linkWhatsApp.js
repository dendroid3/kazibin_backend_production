const puppeteer = require('puppeteer');
const path = require('path');

(async () => {
  const browser = await puppeteer.launch({
    executablePath: '/usr/bin/chromium-browser', // Set path to where chromium is downloaded
    headless: false, // Non-headless mode to show the browser
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
    userDataDir: path.join(__dirname, 'user_data') // Save session data to avoid repeated logins
  });

  const page = await browser.newPage();
  await page.goto('https://web.whatsapp.com', { waitUntil: 'networkidle2' });

  console.log('Please scan the QR code from your mobile device.');
  
  // Keep the browser open to scan the QR code
  await page.waitForTimeout(300000); // 30 seconds should be enough to scan the code

  // Optionally, keep the browser open for a longer time for manual closing
  // await browser.close();
})();