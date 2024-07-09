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

pushd $(cd $(dirname $0);pwd)
  CURRENT_BRANCH=$(git branch --show-current)
  if [ $CURRENT_BRANCH != "master" ]; then
    echo "Not on master branch"
    exit 1
  fi

  pushd deploy
    composer install
  popd

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

  pushd deploy
    ./vendor/bin/dep deploy --tag=$TAG
  popd
popd
