const wdioConfig = require('./wdio.conf.js');

wdioConfig.config.capabilities = [{
    browserName: 'chrome',
}];
wdioConfig.config.baseUrl = 'http://127.0.0.1:8111';
wdioConfig.config.tags = '@test, ~@ci, ~@Pending, ~@setup, ~@deprecated, ~@Bug, ~@smoketest';
wdioConfig.config.services = ['selenium-standalone'];
wdioConfig.config.specs = './src/features/*.feature';
wdioConfig.config.screenshotPath ='./errorShots/';
wdioConfig.config.bail = 0;
exports.config = wdioConfig.config;

