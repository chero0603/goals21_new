// @ts-check
import tailwindcss from '@tailwindcss/vite';
import icon from 'astro-icon';
import { defineConfig } from 'astro/config';

// https://astro.build/config
export default defineConfig({
  integrations: [icon()],
  base: '/goals21/',
  vite: {
    plugins: [tailwindcss()],
  },
});
