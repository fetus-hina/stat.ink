#!/bin/bash

set -eu

function current_version() {
    VTAGS=$(/usr/bin/env git tag -l "v*" --sort="-v:refname")
    for VTAG in $VTAGS; do
        if [[ $VTAG =~ ^v[0-9]+\.[0-9]+\.[0-9]+ ]]; then
            echo $VTAG
            return 0
        fi
    done
    echo "Could not detect current version"
    exit 1
}

if [ $# -ne 1 ]; then
  echo "USAGE: $0 {vA.B.C|minor|patch}"
  exit 1
fi

TAG=""
if [ $1 = "current" ]; then
  current_version
  exit 0
elif [ $1 = "minor" -o $1 = "patch" ]; then
  PREV=$(current_version)
  TAG=v$(npx semver -i $1 $PREV)
elif [[ $1 =~ ^v[0-9]+\.[0-9]+\.[0-9]+ ]]; then
  TAG=$1
fi

if [[ $TAG =~ ^v[0-9]+\.[0-9]+\.[0-9]+ ]]; then
  git tag -s $TAG -m $TAG
else
  echo "Invalid version tag $1. Should be like v1.2.3, 'minor' or 'patch'"
  exit 1
fi

git push origin dev master $TAG
ssh statink@app1.stat.ink 'pushd stat.ink && git fetch origin && git checkout composer.lock package-lock.json && touch -r composer.json composer.lock && touch -r package.json package-lock.json && git merge --ff-only origin/master && scl enable php80 make && scl enable php80 "./yii asset/up-revision" && scl enable php80 "./yii asset/cleanup" && sudo bin/restart-supervisord.sh'
