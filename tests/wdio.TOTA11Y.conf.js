const wdioConfig = require('./wdio.conf.js');

wdioConfig.config.capabilities = [{
    browserName: 'chrome',
}];
wdioConfig.config.baseUrl = 'http://127.0.0.1:8111';
wdioConfig.config.tags = '@ci, ~@Pending, ~@setup, ~@deprecated, ~@Bug, ~@smoketest';
wdioConfig.config.services = ['selenium-standalone'];
wdioConfig.config.specs = './src/features/*.feature';
wdioConfig.config.screenshotPath ='./errorShots/';
wdioConfig.config.bail = 0;
wdioConfig.config.afterStep = function afterStep(stepResult) {
    const isVisible = browser.isVisible('.tota11y-info-error-count');
    browser.element(".tota11y-toolbar-toggle").click();
    browser.element(".tota11y-plugin-title*=Headings").click();
    expect(isVisible).to.not.equal(true);
    browser.element(".tota11y-plugin-title*=Contrast").click();
    expect(isVisible).to.not.equal(true);
    browser.element(".tota11y-plugin-title*=Link text").click();
    expect(isVisible).to.not.equal(true);
    browser.element(".tota11y-plugin-title*=Labels").click();
    expect(isVisible).to.not.equal(true);
    browser.element(".tota11y-plugin-title*=Image alt-text").click();
    expect(isVisible).to.not.equal(true);
    browser.element(".tota11y-toolbar-toggle").click();
};
exports.config = wdioConfig.config;



