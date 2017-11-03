const config = require('./wdio.conf.js').config;

config.capabilities = [{
  browserName: 'phantomjs',
  chromeOptions: {
    binary: '/usr/bin/google-chrome',
    args: ['--headless', '--no-sandbox', '--disable-gpu', '--window-size=1200,2000']
  }
}];
config.screenshotPath = './errorShots/';
config.services = ['selenium-standalone'];
config.baseUrl = 'http://127.0.0.1:80';
config.tags = '@ci, ~@Pending, ~@setup, ~@deprecated, ~@Bug';
config.cucumberOpts.failFast = true;
exports.config = config;
