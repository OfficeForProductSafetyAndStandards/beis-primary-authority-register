const config = require('./wdio.conf.js').config;

config.capabilities = [{
        browser: 'ie',
        browser_version: '8.0',
        device: null,
        os: 'Windows',
        os_version: '7',
        resolution: '1024x768',
        debug: true
}];

config.services = ['browserstack'];
config.user = '';
config.key = '';
config.browserstackLocal = true;
config.host = 'hub-cloud.browserstack.com';
config.baseUrl = '';
config.tags = '@ci, ~@Pending, ~@ie8bug';
exports.config = config;
