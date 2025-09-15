// @ts-check
import tailwindcss from "@tailwindcss/vite";
import icon from "astro-icon";
import { defineConfig } from "astro/config";

// https://astro.build/config
export default defineConfig({
  output: "static",
  integrations: [
    icon({
      iconDir: "src/assets/icons",
      include: {
        mdi: ["*"],
        "mdi-light": ["*"],
        flag: ["*"],
        emojione: ["*"],
        twemoji: ["*"],
      },
    }),
  ],
  base: "/goals21/",
  server: {
    host: "0.0.0.0",
    port: 4321,
  },
  vite: {
    plugins: [tailwindcss()],
  },
});
