#!/usr/bin/env node

'use strict';

const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

const args = process.argv.slice(2);
let input = null;
let output = null;

for (let i = 0; i < args.length; i++) {
  if (args[i] === '-o' && i + 1 < args.length) {
    output = args[++i];
  } else if (!input) {
    input = args[i];
  }
}

if (!input || !output) {
  console.error('Usage: svg2png.js [-o output.png] input.svg');
  process.exit(1);
}

sharp(path.resolve(input))
  .png()
  .toFile(path.resolve(output))
  .then(() => {
    process.exit(0);
  })
  .catch((err) => {
    console.error(err);
    process.exit(1);
  });
