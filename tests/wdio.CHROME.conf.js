const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'chrome',
}];

config.screenshotPath = './errorShots/';
config.services = ['selenium-standalone'];
config.baseUrl = 'http://127.0.0.1:8111';
config.tags = '@ci, ~@Pending, ~@Bug, ~@userjourney1';
config.cucumberOpts.failFast = true;
exports.config = config;
