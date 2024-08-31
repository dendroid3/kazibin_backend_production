#!/bin/bash

# Start Xvfb
Xvfb :99 -screen 0 1280x1024x24 -nolisten tcp -ac +extension GLX +render -noreset &
export DISPLAY=:99

# Start VNC Server
vncserver :99 -geometry 1280x1024 -depth 24

# Run Puppeteer script
node resources/js/node_scripts/sendMessage.js "$1" "$2"
