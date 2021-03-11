#!/bin/bash

set -ue

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"

pushd $DIR >/dev/null 2>&1
  systemctl restart supervisord.service
popd >/dev/null 2>&1
