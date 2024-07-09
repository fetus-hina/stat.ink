#!/bin/bash

set -ue

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

pushd $DIR >/dev/null 2>&1
  systemctl daemon-reload
  systemctl restart 'yii-queue-statink@*'
popd >/dev/null 2>&1
