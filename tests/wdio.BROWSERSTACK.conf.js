const config = require('./wdio.conf.js').config;

config.capabilities = [
    {
        device: 'Google Nexus 9',
        realMobile: 'true',
        os_version: '5.1',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        device: 'Samsung Galaxy S8 Plus',
        realMobile: 'true',
        os_version: '7.0',
        'browserstack.debug': true,
        'browserstack.local': true,
    },
    {
        device: 'iPhone 7',
        realMobile: 'true',
        os_version: '10.0',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        device: 'Samsung Galaxy Note 4',
        realMobile: 'true',
        os_version: '6.0',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        browser: 'ie',
        browser_version: '8.0',
        device: null,
        os: 'Windows',
        os_version: '7',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        browser: 'ie',
        browser_version: '9',
        device: null,
        os: 'Windows',
        os_version: '7',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {

        browser: 'safari',
        browser_version: '9.1',
        device: null,
        os: 'OS X',
        os_version: 'El Capitan',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        browser: 'Edge',
        browser_version: '15',
        device: null,
        os: 'Windows',
        os_version: '10',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
    {
        browser: 'Firefox',
        browser_version: '54',
        device: null,
        os: 'Windows',
        os_version: '10',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    },
];

config.services = ['browserstack'];
config.user = process.env.BSK_USER;
config.key = process.env.BSK_KEY;
config.browserstackLocal = true;
config.host = 'hub-cloud.browserstack.com';
config.baseUrl = 'http://192.168.82.68:8111';
config.tags = '@ci, ~@Pending, ~@ie8bug';
exports.config = config;
