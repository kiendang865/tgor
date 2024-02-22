/*=========================================================================================
  File Name: vue.config.js
  Description: configuration file of vue
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

const path = require("path");
const loader = {
  loader: "sass-loader",
  options: {
    resources: path.resolve(__dirname, "./scss/app.scss"),
  },
};
module.exports = {
  configureWebpack: {
    module: {
      rules: [
        {
          test: /\.scss$/,
          use: [loader, "sass-loader"],
        },
      ],
    },
    resolve: {
      alias: {
        "@": path.resolve(__dirname, 'src')
      }
    },
  },
  resolve: {
    alias: {
      // Alias for using source of BootstrapVue
      "bootstrap-vue$": "bootstrap-vue/src/index.js",
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          // Exclude transpiling `node_modules`, except `bootstrap-vue/src`
          exclude: /node_modules\/(?!bootstrap-vue\/src\/)/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["env"],
            },
          },
        },
        {
          test: /\.css$/i,
          use: [
            "style-loader",
            "css-loader",
            "handlebars-loader", // handlebars loader expects raw resource string
            "extract-loader",
            "css-loader",
          ],
        },
      ],
    },
    css: {
      loaderOptions: {
        // pass options to sass-loader
        // @/ is an alias to src/
        // so this assumes you have a file named `src/variables.sass`
        // Note: this option is named as "data" in sass-loader v7
        sass: {
          data: `@import "@/scss/config/_variables.scss";`,
        },
        // by default the `sass` option will apply to both syntaxes
        // because `scss` syntax is also processed by sass-loader underlyingly
        // but when configuring the `data` option
        // `scss` syntax requires an semicolon at the end of a statement, while `sass` syntax requires none
        // in that case, we can target the `scss` syntax separately using the `scss` option
        scss: {
          prependData: `@import "~@/variables.scss";`,
        },
        // pass Less.js Options to less-loader
        less: {
          // http://lesscss.org/usage/#less-options-strict-units `Global Variables`
          // `primary` is global variables fields name
          globalVars: {
            primary: "#fff",
          },
        },
      },
    },
  },
};
