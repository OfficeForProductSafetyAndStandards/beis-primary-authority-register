const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

When('the EN emails confirmations for {string} are processed for enforcement notication {string}', function (string, string2) {
return client
    .url(client.launch_url + '/user/logout')
    .url(client.launch_url + '/user/login')
    .setValue('#edit-name','dadmin')
    .setValue('#edit-pass','TestPassword')
    .click('#edit-submit')
    .url(client.launch_url + '/admin/reports/maillog')
    .setValue('#edit-subject','Primary Authority - Notification of Proposed Enforcement')
    .setValue('#edit-header-to', string)
    .click('#edit-submit-maillog-overview')
return shared
    .clickLinkByPureText(string2)
return client
    .contains('h1.heading-xlarge','Primary Authority - Notification of Proposed Enforcement')
    .contains('block-par-theme-content',string)
});