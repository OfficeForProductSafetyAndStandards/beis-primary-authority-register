const config = require('./wdio.conf.js').config;

config.capabilities = [{
    browserName: 'chrome',
    chromeOptions: {
        args: [
            'headless',
            'disable-gpu',
            'window-size=1366,768',
        ],
    },
}];

config.services = ['chromedriver'];
config.baseUrl = 'http://127.0.0.1:80';
config.tags = '@ci, ~@Pending';
exports.config = config;
