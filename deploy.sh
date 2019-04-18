#!/bin/bash

set -eu

if [ $# -ne 1 ]; then
  echo "USAGE: $0 vA.B.C"
  exit 1
fi

if [[ $1 =~ ^v[0-9]+\.[0-9]+\.[0-9]+ ]]; then
  git tag -s $1 -m $1
else
  echo "Invalid version tag $1. Should be like v1.2.3"
  exit 1
fi

git push origin master $1
ssh statink@app1.stat.ink 'pushd stat.ink && git fetch origin && git checkout composer.lock package-lock.json && git merge --ff-only origin/master && scl enable php73 make && touch .maintenance && rm -rfv web/assets/* runtime/Smarty/compile/* && rm -f .maintenance'
