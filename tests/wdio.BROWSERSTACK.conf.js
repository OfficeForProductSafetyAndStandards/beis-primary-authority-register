const config = require('./wdio.conf.js').config;

config.services = ['browserstack'];
config.user = process.env.BROWSERSTACK_USERNAME;
config.key = process.env.BROWSERSTACK_ACCESS_KEY;
config.browserstackLocal = true;
config.host = 'hub-cloud.browserstack.com';
config.baseUrl = 'http://localhost:8111';
config.tags = '@ci, ~@Pending';
exports.config = config;
