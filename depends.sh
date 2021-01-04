#!/bin/bash

set -eu

function has_change() {
    if git diff --no-ext-diff --quiet --exit-code; then
        return 1 # not changed if the command successed
    else
        return 0 # changed if the command failed
    fi
}

BASEDIR=$(dirname $0)
cd "$BASEDIR"

if has_change; then
    echo "This repository has been changed. Abort."
    exit 1
fi

make composer.phar
./composer.phar update

npx updates -u -m
rm -rf node_modules package-lock.json
npm update

if ! has_change; then
    echo "Not changed."
    exit 0
fi

git checkout -b updates-`date '+%Y%m%d'`
git commit -a -m '[no author] Update dependencies' -S
