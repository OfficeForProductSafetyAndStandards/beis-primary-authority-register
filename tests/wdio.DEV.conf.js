const wdioConfig = require('./wdio.conf.js');

wdioConfig.config.capabilities = [{
    browserName: 'chrome',
}];
wdioConfig.config.baseUrl = 'http://127.0.0.1:8111';
wdioConfig.config.tags = '@ci, ~@Pending, ~@Bug';

exports.config = wdioConfig.config;
