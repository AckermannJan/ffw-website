module.exports = {
  root: true,
  env: {
    node: true,
    browser: true,
    es2022: true,
  },
  extends: [
    "plugin:vue/vue3-recommended",
    "eslint:recommended",
    "@vue/eslint-config-prettier",
  ],
  parserOptions: {
    ecmaVersion: 2022,
    sourceType: "module",
  },
  rules: {
    "no-console": process.env.NODE_ENV === "production" ? "warn" : "off",
    "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off",
    "vue/no-v-text-v-html-on-component": "off",
    "vue/multi-word-component-names": "off",
  },
  ignorePatterns: ["node_modules", "dist"],
};
