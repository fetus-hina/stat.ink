#!/bin/bash

set -eu
cd $(cd $(dirname $0); pwd)

rm -f dbs.py dbs.pyc
curl -sSL -o dbs.py https://github.com/frozenpandaman/splatnet2statink/raw/master/dbs.py
./db2json
