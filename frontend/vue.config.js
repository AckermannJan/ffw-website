module.exports = {
  transpileDependencies: ["vuetify"],
  devServer: {
    open: process.platform === "darwin",
    host: "fft.local",
    port: 8080,
    https: true,
    hotOnly: false
  }
};
