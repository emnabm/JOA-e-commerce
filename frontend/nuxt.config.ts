// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: true },

  modules: [
    '@nuxt/content',
    '@nuxt/eslint',
    '@nuxt/fonts',
    '@nuxt/icon',
    '@nuxt/image',
    '@nuxt/scripts',
    '@nuxt/test-utils',
    '@nuxt/ui'
  ],
  ssr: false, // Désactive le SSR si pas nécessaire
  vite: {
    define: {
      global: 'globalThis',
    }
  },
  css:
      ['bootstrap/dist/css/bootstrap.min.css'],

})