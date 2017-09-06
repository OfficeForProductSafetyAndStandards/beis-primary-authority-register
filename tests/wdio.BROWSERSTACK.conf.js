const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browser: 'ie',
    browser_version: '8.0',
    device: null,
    os: 'Windows',
    os_version: '7',
    resolution: '1024x768',
    'browserstack.local': true,
    'browserstack.debug': true
},
    {
        browser: 'ie',
        browser_version: '9',
        device: null,
        os: 'Windows',
        os_version: '7',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true
    },
    {

        browser: 'safari',
        browser_version: '9.1',
        device: null,
        os: 'OS X',
        os_version: 'El Capitan',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true
    },
    {
        browser: 'Edge',
        browser_version: '15',
        device: null,
        os: 'Windows',
        os_version: '10',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true
    },
    {
        browser: 'Firefox',
        browser_version: '54',
        device: null,
        os: 'Windows',
        os_version: '10',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true
    },
    {
        browser: 'Chrome',
        browser_version: '60',
        device: null,
        os: 'Windows',
        os_version: '8',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true
    }
];

config.services = ['browserstack'];
config.user = 'finance9';
config.key = 'hpz8xxcwr3QVhFWwLWvR';
config.browserstackLocal = true;
config.host = 'hub-cloud.browserstack.com';
config.baseUrl = 'http://localhost:8111';
config.tags = '@ci, ~@Pending, ~@ie8bug';
exports.config = config;
