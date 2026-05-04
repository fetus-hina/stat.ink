import neostandard from 'neostandard';

export default [
  ...neostandard({
    semi: true,
    globals: ['$', 'jQuery'],
    files: ['**/*.es']
  }),
  {
    ignores: ['resources/.compiled/**']
  }
];
