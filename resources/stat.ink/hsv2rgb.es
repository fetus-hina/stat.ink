window.hsv2rgb = function hsv2rgb (h, s, v) {
  let r, g, b;
  while (h < 0) {
    h += 360;
  }
  h = Math.floor(h) % 360;
  v = v * 255;
  if (s === 0) {
    r = g = b = v;
  } else {
    const i = Math.floor(h / 60) % 6;
    const f = (h / 60) - i;
    const p = v * (1 - s);
    const q = v * (1 - f * s);
    const t = v * (1 - (1 - f) * s);

    switch (i) {
      case 0:
        r = v;
        g = t;
        b = p;
        break;

      case 1:
        r = q;
        g = v;
        b = p;
        break;

      case 2:
        r = p;
        g = v;
        b = t;
        break;

      case 3:
        r = p;
        g = q;
        b = v;
        break;

      case 4:
        r = t;
        g = p;
        b = v;
        break;

      case 5:
        r = v;
        g = p;
        b = q;
        break;
    }
  }
  return [
    Math.round(r),
    Math.round(g),
    Math.round(b)
  ];
};
