const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'chrome',
}];

config.screenshotPath = './errorShots/';
config.services = ['selenium-standalone'];
config.baseUrl = 'https://demo-cdn.par-beta.co.uk';
config.tags = '@ci';
config.cucumberOpts.failFast = true;
exports.config = config;
