#!/bin/bash

# Start Xvfb
Xvfb :99 -screen 0 1280x1024x24 &
export DISPLAY=:99

# Run Puppeteer script
node resources/js/node_scripts/sendMessage.js "$1" "$2"
