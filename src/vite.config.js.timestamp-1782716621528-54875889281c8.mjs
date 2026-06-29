// vite.config.js
import { defineConfig } from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import tailwindcss from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/@tailwindcss/vite/dist/index.mjs";
import path from "path";
var __vite_injected_original_dirname = "C:\\praktikum-rpl-A-7-dev\\src";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true
    }),
    tailwindcss(),
    vue()
  ],
  resolve: {
    alias: {
      // Alias @ → resources/js
      // Gunakan import '@/stores/auth' alih-alih '../../../stores/auth'
      "@": path.resolve(__vite_injected_original_dirname, "resources/js")
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxwcmFrdGlrdW0tcnBsLUEtNy1kZXZcXFxcc3JjXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxwcmFrdGlrdW0tcnBsLUEtNy1kZXZcXFxcc3JjXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9wcmFrdGlrdW0tcnBsLUEtNy1kZXYvc3JjL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCB0YWlsd2luZGNzcyBmcm9tICdAdGFpbHdpbmRjc3Mvdml0ZSdcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnXG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbJ3Jlc291cmNlcy9jc3MvYXBwLmNzcycsICdyZXNvdXJjZXMvanMvYXBwLmpzJ10sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICAgICAgdGFpbHdpbmRjc3MoKSxcbiAgICAgICAgdnVlKCksXG4gICAgXSxcbiAgICByZXNvbHZlOiB7XG4gICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICAvLyBBbGlhcyBAIFx1MjE5MiByZXNvdXJjZXMvanNcbiAgICAgICAgICAgIC8vIEd1bmFrYW4gaW1wb3J0ICdAL3N0b3Jlcy9hdXRoJyBhbGloLWFsaWggJy4uLy4uLy4uL3N0b3Jlcy9hdXRoJ1xuICAgICAgICAgICAgJ0AnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAncmVzb3VyY2VzL2pzJyksXG4gICAgICAgIH0sXG4gICAgfSxcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTRRLFNBQVMsb0JBQW9CO0FBQ3pTLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFDaEIsT0FBTyxpQkFBaUI7QUFDeEIsT0FBTyxVQUFVO0FBSmpCLElBQU0sbUNBQW1DO0FBTXpDLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU8sQ0FBQyx5QkFBeUIscUJBQXFCO0FBQUEsTUFDdEQsU0FBUztBQUFBLElBQ2IsQ0FBQztBQUFBLElBQ0QsWUFBWTtBQUFBLElBQ1osSUFBSTtBQUFBLEVBQ1I7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQTtBQUFBO0FBQUEsTUFHSCxLQUFLLEtBQUssUUFBUSxrQ0FBVyxjQUFjO0FBQUEsSUFDL0M7QUFBQSxFQUNKO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
