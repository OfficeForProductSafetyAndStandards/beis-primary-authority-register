const wdioConfig = require('./wdio.conf.js');

wdioConfig.config.capabilities = [{
    browserName: 'chrome',
}];
wdioConfig.config.baseUrl = 'http://127.0.0.1:8111';
wdioConfig.config.tags = '@smoketest, ~@ci, ~@Pending, ~@Bug';
wdioConfig.config.services = ['selenium-standalone'];
wdioConfig.config.specs = './src/features/smoketest/*.feature';
exports.config = wdioConfig.config;

