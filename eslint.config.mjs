import neostandard from 'neostandard';

export default [
  ...neostandard({ semi: true, globals: ['$', 'jQuery'] }),
  {
    ignores: ['resources/.compiled/**']
  }
];
