#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

pushd $DIR >/dev/null 2>&1
  npx patch-package --patch-dir=data/patch/npm
  rm -rf $DIR/data/licenses-npm
  mkdir -p $DIR/data/licenses-npm
  npx license-checker-rseidelsohn --production --files $DIR/data/licenses-npm/ >/dev/null

  rm -rf $DIR/node_modules/twemoji-emojis
  $DIR/bin/twemoji-download $DIR/node_modules/twemoji-emojis/vendor/
popd >/dev/null 2>&1
