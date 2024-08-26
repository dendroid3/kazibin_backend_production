const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

// Get command-line arguments
const args = process.argv.slice(2);
const groupName = args[0];
const message = args[1];

(async () => {
  // Launch browser
  const browser = await puppeteer.launch({
    // executablePath: '/usr/bin/chromium-browser', // Set path to where chromium is downloaded
    headless: false,  // Run headless for server environment
    args: ['--no-sandbox', '--disable-setuid-sandbox'], // Required for running in a Docker or certain Linux environments
    userDataDir: path.join(__dirname, 'user_data') // Save session data here
  });
  const page = await browser.newPage();

  // Navigate to WhatsApp Web
  await page.goto('https://web.whatsapp.com');

  page.setDefaultTimeout(0)
  // Delay to ensure elements are fully loaded
  // await page.waitForTimeout(5000);

  // Check if already logged in
  if (!page.$(`span[title="${groupName}"]`)) {
    console.log('Please scan the QR code to login.');
    fs.writeFile('log.txt', "Please scan the QR code to login.", err => {
      if (err) {
        console.error(err);
      } else {
        // file written successfully
      }
    });
    await page.waitForSelector(`span[title="${groupName}"]`);
  } else {
    console.log('Logged in.');
    fs.writeFile('log.txt', "Logged in.", err => {
      if (err) {
        console.error(err);
      } else {
        // file written successfully
      }
    });
  }

  // Search for the group
  await page.waitForSelector(`span[title="${groupName}"]`, { timeout: 300000 });
  await page.click(`span[title="${groupName}"]`);

  // Wait for the chat to load
  await page.waitForSelector('._ak1r');
  
  // Type the message in the message box
  const messageBox = await page.$('._ak1r');
  
  const lines = message.split('--');

  for (let i = 0; i < lines.length; i++) {
      await messageBox.type(lines[i]);
      if (i < lines.length - 1) {
      // await messageBox.type(lines[1]);

        await page.keyboard.down('Shift');
        await page.keyboard.press('Enter');
        await page.keyboard.up('Shift');
    }
  }

  // Send the message
  await page.keyboard.press('Enter');

  // Close the browser
  setTimeout(() => {
    browser.close();
  }, 15000);
})();
