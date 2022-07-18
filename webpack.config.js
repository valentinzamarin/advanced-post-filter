const path = require("path");

module.exports = {
  entry: {
    "admin/js/apf-admin": "./src/admin/index.js",
    "public/js/apf-public": "./src/public/index.js",
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname),
  },
};
