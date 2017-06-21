const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'phantomjs',
}];

config.services = ['phantomjs'];
config.baseUrl = 'http://127.0.0.1:80';

exports.config = config;
