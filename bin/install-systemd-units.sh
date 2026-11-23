#!/bin/bash

# Install stat.ink's systemd units into /etc/systemd/system as regular files.
#
# The units must not be symlinks into the deploy directory: systemd loads unit
# files before /var/www/sites is mounted, so such symlinks are dangling at boot
# and the units go missing until the next daemon-reload.

set -euo pipefail
shopt -s nullglob

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." >/dev/null 2>&1 && pwd )"
SRC_DIR="${DIR}/data/systemd"
DST_DIR=/etc/systemd/system

TIMERS=()
for SRC in "${SRC_DIR}"/statink-*.service "${SRC_DIR}"/statink-*.timer; do
  NAME="$(basename "${SRC}")"
  DST="${DST_DIR}/${NAME}"

  # Remove an old symlink first so that we never write through it
  if [ -L "${DST}" ]; then
    rm -f "${DST}"
  fi

  install -Z -m 0644 -o root -g root "${SRC}" "${DST}"

  if [[ "${NAME}" == *.timer ]]; then
    TIMERS+=("${NAME}")
  fi
done

systemctl daemon-reload

if [ ${#TIMERS[@]} -gt 0 ]; then
  systemctl enable "${TIMERS[@]}"
fi
