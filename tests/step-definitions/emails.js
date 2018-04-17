const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

When('the enforcement notification emails confirmations for {string} are processed for enforcement notication {string}', function (string, string2) {
    return shared
    .clickLinkByPureText('Log out')
    .clickLinkByPureText('Log in')
    .setValue('#edit-name','dadmin')
    .setValue('#edit-pass','TestPassword')
    .click('#edit-submit')
    .url(client.launch_url + '/admin/reports/maillog')
    .click('//*[@id="block-seven-content"]/div/div/div[3]/table/tbody/tr[1]/td[3]/a')
    .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
    .contains('block-par-theme-content',string)
});

When('the enforcement notification approval emails confirmations for {string} are processed for partnership {string}', function (string, string2) {
    return shared
    .clickLinkByPureText('Log out')
    .clickLinkByPureText('Log in')
    .setValue('#edit-name','dadmin')
    .setValue('#edit-pass','TestPassword')
    .click('#edit-submit')
    .url(client.launch_url + '/admin/reports/maillog')
    .click('//*[@id="block-seven-content"]/div/div/div[3]/table/tbody/tr[1]/td[3]/a')
    .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
    .contains('block-par-theme-content',string)
});

When('the partnership creation emails confirmations for {string} are processed for partnership {string}', function (string, string2) {
    return shared
        .clickLinkByPureText('Log out')
        .clickLinkByPureText('Log in')
        .setValue('#edit-name','dadmin')
        .setValue('#edit-pass','TestPassword')
        .click('#edit-submit')
        .url(client.launch_url + '/admin/reports/maillog')
        .click('//*[@id="block-seven-content"]/div/div/div[3]/table/tbody/tr[1]/td[3]/a')
        .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
        .contains('block-par-theme-content',string)
    });

When('the partnership update emails confirmations for {string} are processed for partnership {string}', function (string, string2) {
    return shared
    .clickLinkByPureText('Log out')
    .clickLinkByPureText('Log in')
    .setValue('#edit-name','dadmin')
    .setValue('#edit-pass','TestPassword')
    .click('#edit-submit')
    .url(client.launch_url + '/admin/reports/maillog')
    .click('//*[@id="block-seven-content"]/div/div/div[3]/table/tbody/tr[1]/td[3]/a')
    .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
    .contains('block-par-theme-content',string)
});

When('the partnership approval emails confirmations for {string} are processed for partnership {string}', function (string, string2) {
    return shared
    .clickLinkByPureText('Log out')
    .clickLinkByPureText('Log in')
    .setValue('#edit-name','dadmin')
    .setValue('#edit-pass','TestPassword')
    .click('#edit-submit')
    .url(client.launch_url + '/admin/reports/maillog')
    .click('//*[@id="block-seven-content"]/div/div/div[3]/table/tbody/tr[1]/td[3]/a')
    .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
    .contains('block-par-theme-content',string)
});
