const wdioConfig = require('./wdio.conf.js');
wdioConfig.config.capabilities = [{
    browserName: 'chrome',
    chromeOptions: {
        binary: '/usr/bin/google-chrome ',
        args: ['--headless', '--no-sandbox', '--disable-gpu', '--window-size=1200,2000']
    }
}];
wdioConfig.config.baseUrl = 'http://127.0.0.1:80';
wdioConfig.config.tags = '@ci, ~@Pending, ~@setup, ~@deprecated, ~@Bug, ~@smoketest';
wdioConfig.config.bail = 1;
wdioConfig.config.cucumberOpts.failFast = true;
exports.config = wdioConfig.config;
