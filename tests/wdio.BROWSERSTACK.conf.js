const wdioConfig = require('./wdio.conf.js');

wdioConfig.config.capabilities = [
    //{
    //    device: 'Google Nexus 9',
    //    realMobile: 'true',
    //    os_version: '5.1',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    //{
    //    device: 'Samsung Galaxy S8 Plus',
    //    realMobile: 'true',
    //    os_version: '7.0',
    //    'browserstack.debug': true,
    //    'browserstack.local': true,
    //},
    //{
    //    device: 'iPhone 7 Plus',
    //    realMobile: 'true',
    //    os_version: '10.3',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    //{
    //    device: 'Samsung Galaxy Note 4',
    //    realMobile: 'true',
    //    os_version: '6.0',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    //{
    //    browser: 'ie',
    //    browser_version: '8.0',
    //    device: null,
    //    os: 'Windows',
    //    os_version: '7',
    //    resolution: '1024x768',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    //{
    //    browser: 'ie',
    //    browser_version: '9',
    //    device: null,
    //    os: 'Windows',
    //    os_version: '7',
    //    resolution: '1024x768',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    //{
    //
    //    browser: 'safari',
    //    browser_version: '9.1',
    //    device: null,
    //    os: 'OS X',
    //    os_version: 'El Capitan',
    //    resolution: '1024x768',
    //    'browserstack.local': true,
    //    'browserstack.debug': true,
    //},
    {
        browser: 'Edge',
        browser_version: '15',
        device: null,
        os: 'Windows',
        os_version: '10',
        resolution: '1024x768',
        'browserstack.local': true,
        'browserstack.debug': true,
    }
];

wdioConfig.config.services = ['browserstack'];
wdioConfig.config.user = process.env.BSK_USER;
wdioConfig.config.key = process.env.BSK_KEY;
wdioConfig.config.browserstackLocal = true;
wdioConfig.config.host = 'hub-cloud.browserstack.com';
wdioConfig.config.baseUrl = 'http://192.168.82.68:8111';
wdioConfig.config.tags = '@ci, ~@Pending, ~@ie8bug';
wdioConfig.config.timeout = 100000;
wdioConfig.config.waitforTimeout = 100000;
exports.config = wdioConfig.config;
