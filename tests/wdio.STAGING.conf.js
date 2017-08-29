const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'chrome',
}];

config.screenshotPath = './errorShots/';
config.services = ['selenium-standalone'];
config.baseUrl = 'http://par-beta-staging.cloudapps.digital';
config.tags = '@ci, ~@Pending, ~@Bug, ~@userjourney1';
config.cucumberOpts.failFast = true;
exports.config = config;

