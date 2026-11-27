#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

pushd $DIR >/dev/null 2>&1
  npx --no -- patch-package --patch-dir=data/patch/npm
  rm -rf $DIR/data/licenses-npm
  mkdir -p $DIR/data/licenses-npm
  npx --no -- license-checker-rseidelsohn --files $DIR/data/licenses-npm/ >/dev/null

  # Download the images of the same version as the @twemoji/api package
  TWEMOJI_VERSION="$(node -p "require('@twemoji/api/package.json').version")"
  rm -rf $DIR/node_modules/twemoji-emojis
  $DIR/bin/twemoji-download $DIR/node_modules/twemoji-emojis/vendor/ "$TWEMOJI_VERSION"
popd >/dev/null 2>&1
