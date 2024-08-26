#!/bin/bash

# Start Xvfb
Xvfb :99 -screen 0 1280x1024x24 &
export DISPLAY=:99

# Start VNC server (optional but recommended for remote access)
vncserver :99 -geometry 1280x1024 -depth 24

# Run Puppeteer script
node resources/js/node_scripts/linkWhatsApp.js
