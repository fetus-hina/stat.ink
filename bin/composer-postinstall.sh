#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

pushd $DIR >/dev/null 2>&1
  rm -rf $DIR/data/licenses-composer
  mkdir -p $DIR/data/licenses-composer
  $DIR/yii license/extract --interactive=0
  $DIR/vendor/bin/codecept build
popd >/dev/null 2>&1
