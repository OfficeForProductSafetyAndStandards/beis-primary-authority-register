const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'phantomjs',
}];

// config.services = ['selenium-'];
config.baseUrl = 'http://127.0.0.1:80';
config.tags = '@ci, ~@Pending';
exports.config = config;
