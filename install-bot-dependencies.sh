#!/bin/bash
# Script to download dependencies for sekhmet.py
echo 'Downloading python dependencies'
wget https://raw.githubusercontent.com/jbalogh/python-irclib/master/ircbot.py -P bot/
wget https://raw.githubusercontent.com/jbalogh/python-irclib/master/irclib.py -P bot/
echo 'Downloading complete'
