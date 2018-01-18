#!/bin/bash

set -eu

git push origin master
ssh statink@app1.stat.ink 'pushd stat.ink && git fetch origin && git merge --ff-only origin/master && scl enable php71 make && rm -rfv web/assets/* runtime/Smarty/compile/*'
