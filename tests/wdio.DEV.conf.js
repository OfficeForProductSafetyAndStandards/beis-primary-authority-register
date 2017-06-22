const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'chrome',
}];

config.baseUrl = 'http://127.0.0.1:8111';

exports.config = config;
