import { defineConfig } from 'vite';

export default defineConfig({
  root: 'assets',
  build: {
    emptyOutDir: true,
    outDir: '../public/build/assets',
    rollupOptions: {
      input: {
        main: 'assets/script.js',
        style: 'assets/style.css'
      },
      output: {
        entryFileNames: '[name].js',
        assetFileNames: '[name][extname]'
      }
    }
  }
});