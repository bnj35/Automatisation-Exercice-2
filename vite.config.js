import { defineConfig } from 'vite';

export default defineConfig({
  root: 'assets',
  build: {
    emptyOutDir: true,
    outDir: '../public/build',
    rollupOptions: {
      input: {
        main: 'assets/script.js',
        style: 'assets/style.css'
      }
    }
  }
});