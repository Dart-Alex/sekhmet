#!/bin/bash
# Script to download dependencies for sekhmet.py
echo 'Downloading python dependencies'
php -r "copy('https://raw.githubusercontent.com/jbalogh/python-irclib/master/ircbot.py', 'bot/ircbot.py');"
php -r "copy('https://raw.githubusercontent.com/jbalogh/python-irclib/master/irclib.py', 'bot/irclib.py');"
echo 'Downloading complete'
