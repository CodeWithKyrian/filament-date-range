const cssbyebye = require('css-byebye');
const cssnano = require('cssnano');

const rulesToRemove = [
  // Accessibility
  /^\.sr-only$/,

  // Position
  /^\.(absolute|fixed|relative|static)$/,

  // Layout
  /^\.container$/,

  // Margin
  /^\.-?m[xytblr]-\d+$/,
  /^\.mx-auto$/,

  // Display
  /^\.(block|flex|grid|inline|inline-block|inline-flex|table)$/,

  // Height & Width
  /^\.[hw]-(\d+|full|screen)$/,

  // Cursor
  /^\.cursor-pointer$/,

  // Flex
  /^\.flex-(col|wrap)$/,
  /^\.items-center$/,
  /^\.justify-(between|center)$/,

  // Overflow
  /^\.overflow-hidden$/,

  // Border
  /^\.border(-0|-r|-l)?$/,
  /^\.border-(gray-300|transparent)$/,

  // Background
  /^\.bg-(blue-100|blue-200|blue-800|gray-200|white)$/,

  // Padding
  /^\.p-\d+$/,
  /^\.p[xy]-\d+$/,

  // Typography
  /^\.text-(center|lg|sm|xs)$/,
  /^\.text-(gray-500|gray-600|gray-700|gray-800|white)$/,
  /^\.leading-none$/,
  /^\.font-(bold|medium|normal|semibold)$/,
  /^\.antialiased$/,

  // Shadow, filter, transition
  /^\.shadow$/,
  /^\.filter$/,
  /^\.transition$/,
  /^\.duration-\d+$/,

  // Variant classes (hover, focus, ltr, rtl)
  /^\.hover\\:bg-gray-200$/,
  /^\.focus\\:outline-none$/,
];

var options = {rulesToRemove: rulesToRemove, map: false}

module.exports = {
  plugins: [
    cssbyebye(options),
    cssnano(),
  ],
};