#!/bin/bash

set -eu
cd $(cd $(dirname $0); pwd)

echo "Downloading Eli's DB..."

rm -f dbs.py dbs.pyc
curl -sSL -o dbs.py https://github.com/frozenpandaman/splatnet2statink/raw/master/dbs.py

echo "Downloaded the DB."
echo "Converting to JSON..."

./db2json


