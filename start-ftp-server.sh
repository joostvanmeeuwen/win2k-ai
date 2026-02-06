#!/bin/bash

VENV_DIR="venv_ftp"
PORT=2121
HOST_IP=$(ip -4 addr | grep -oP '(?<=inet\s)192\.168\.100\.\d+' | head -n 1)

if [ -z "$HOST_IP" ]; then
    HOST_IP="192.168.100.1"
fi

if [ ! -d "$VENV_DIR" ]; then
    python3 -m venv "$VENV_DIR"
    
    source "$VENV_DIR/bin/activate"
    
    pip install --upgrade pip
    pip install pyftpdlib
    
    echo "Installation complete"
else
    echo "Virtual Environment found. Starting..."
    source "$VENV_DIR/bin/activate"
fi

echo "-  Shared folder: $(pwd)"
echo "-  Add Network Place in Windows 2000:"
echo ""
echo -e "   ftp://$HOST_IP:$PORT/"
echo ""

python -m pyftpdlib -w -p $PORT
