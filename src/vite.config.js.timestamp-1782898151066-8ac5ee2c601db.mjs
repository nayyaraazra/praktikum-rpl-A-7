// vite.config.js
import { defineConfig } from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import tailwindcss from "file:///C:/praktikum-rpl-A-7-dev/src/node_modules/@tailwindcss/vite/dist/index.mjs";
import path from "path";
var __vite_injected_original_dirname = "C:\\praktikum-rpl-A-7-dev\\src";
var vite_config_default = defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: [
        "resources/views/**",
        "resources/js/**",
        "resources/css/**",
        "routes/**"
      ]
    }),
    tailwindcss()
  ],
  server: {
    watch: {
      ignored: [
        "**/kulaan_db",
        "**/storage/**",
        "**/database/**"
      ]
    }
  },
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxwcmFrdGlrdW0tcnBsLUEtNy1kZXZcXFxcc3JjXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFxwcmFrdGlrdW0tcnBsLUEtNy1kZXZcXFxcc3JjXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi9wcmFrdGlrdW0tcnBsLUEtNy1kZXYvc3JjL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCB0YWlsd2luZGNzcyBmcm9tICdAdGFpbHdpbmRjc3Mvdml0ZSdcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnXG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICB2dWUoKSxcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogWydyZXNvdXJjZXMvY3NzL2FwcC5jc3MnLCAncmVzb3VyY2VzL2pzL2FwcC5qcyddLFxuICAgICAgICAgICAgcmVmcmVzaDogW1xuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvdmlld3MvKionLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvKionLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzLyoqJyxcbiAgICAgICAgICAgICAgICAncm91dGVzLyoqJyxcbiAgICAgICAgICAgIF0sXG4gICAgICAgIH0pLFxuICAgICAgICB0YWlsd2luZGNzcygpLFxuICAgIF0sXG4gICAgc2VydmVyOiB7XG4gICAgICAgIHdhdGNoOiB7XG4gICAgICAgICAgICBpZ25vcmVkOiBbXG4gICAgICAgICAgICAgICAgJyoqL2t1bGFhbl9kYicsXG4gICAgICAgICAgICAgICAgJyoqL3N0b3JhZ2UvKionLFxuICAgICAgICAgICAgICAgICcqKi9kYXRhYmFzZS8qKicsXG4gICAgICAgICAgICBdLFxuICAgICAgICB9LFxuICAgIH0sXG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgLy8gQWxpYXMgQCBcdTIxOTIgcmVzb3VyY2VzL2pzXG4gICAgICAgICAgICAvLyBHdW5ha2FuIGltcG9ydCAnQC9zdG9yZXMvYXV0aCcgYWxpaC1hbGloICcuLi8uLi8uLi9zdG9yZXMvYXV0aCdcbiAgICAgICAgICAgICdAJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJ3Jlc291cmNlcy9qcycpLFxuICAgICAgICB9LFxuICAgIH0sXG59KVxuXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQTRRLFNBQVMsb0JBQW9CO0FBQ3pTLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFDaEIsT0FBTyxpQkFBaUI7QUFDeEIsT0FBTyxVQUFVO0FBSmpCLElBQU0sbUNBQW1DO0FBTXpDLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLElBQUk7QUFBQSxJQUNKLFFBQVE7QUFBQSxNQUNKLE9BQU8sQ0FBQyx5QkFBeUIscUJBQXFCO0FBQUEsTUFDdEQsU0FBUztBQUFBLFFBQ0w7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsSUFDSixDQUFDO0FBQUEsSUFDRCxZQUFZO0FBQUEsRUFDaEI7QUFBQSxFQUNBLFFBQVE7QUFBQSxJQUNKLE9BQU87QUFBQSxNQUNILFNBQVM7QUFBQSxRQUNMO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQTtBQUFBO0FBQUEsTUFHSCxLQUFLLEtBQUssUUFBUSxrQ0FBVyxjQUFjO0FBQUEsSUFDL0M7QUFBQSxFQUNKO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
