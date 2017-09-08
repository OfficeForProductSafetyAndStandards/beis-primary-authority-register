const config = require('./wdio.conf.js').config;
config.capabilities = [{
    browserName: 'iPad',
    platform: 'MAC',
    device: 'iPad Pro',
},
{
    device: 'Samsung Galaxy S8 Plus',
    realMobile: 'true',
    os_version: '7.0',
},
{
    device: 'iPhone 7 Plus',
    realMobile: 'true',
    os_version: '10.0',
},
{
    browserName: 'iPad',
    platform: 'MAC',
    device: 'iPad Mini 2',
},
];
config.services = ['browserstack'];
config.user = process.env.BSK_USER;
config.key = process.env.BSK_KEY;
config.browserstackLocal = true;
config.host = 'hub-cloud.browserstack.com';
config.baseUrl = 'http://localhost:8111';
config.tags = '@ci, ~@Pending, ~@ie8bug';
exports.config = config;
