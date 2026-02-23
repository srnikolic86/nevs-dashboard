import pluginVue from 'eslint-plugin-vue'

export default [
  ...pluginVue.configs['flat/vue3-essential'],
  {
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module'
    },
    rules: {}
  }
]
