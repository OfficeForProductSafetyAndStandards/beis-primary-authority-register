const config = require('./wdio.conf.js').config;

config.baseUrl = 'http://127.0.0.1:80';
config.tags = '@ci, ~@Pending, ~@Bug';
exports.config = config;
