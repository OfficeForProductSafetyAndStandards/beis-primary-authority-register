const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

// Starting point: enforcement notifications received dashboard
When('I successfully approve enforcement notice {string}', function (string) {
return shared
    .clickLinkByPureText(string)
    .assert.containsText('#edit-actions-0-primary-authority-status--wrapper h3.heading-medium', 'Decide to allow or block this action, or refer this action to another Primary Authority')
    .click('#edit-actions-0-primary-authority-status-approved')
    .click('#edit-actions-next')
    .assert.containsText('#edit-enforcement-summary', 'Summary of enforcement notice')
    .assert.containsText('h1.heading-xlarge', 'Enforcement action decision')
    .click('#edit-actions-next')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPureText('See enforcement notifications sent')
    .assert.containsText('.cols-6', string)
    .assert.containsText('.cols-6', 'Approved')
});

// Starting point: enforcement notifications received dashboard
When('I successfully block enforcement notice {string}', function (string) {
    return shared
    .clickLinkByPureText(string)
    .click('#edit-actions-0-primary-authority-status-blocked')
    .setValue('#edit-actions-0-primary-authority-notes', 'Some notes about why enforcement action blocked')
    .click('#edit-actions-next')
    .assert.containsText('h1.heading-xlarge', 'Enforcement action decision')
    .click('#edit-actions-next')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPureText('See enforcement notifications sent')
    .assert.containsText('.cols-6', string)
    .assert.containsText('.cols-6', 'Blocked')
});

// Starting point: enforcement notifications received dashboard
When('I successfully refer enforcement notice {string}', function (string) {
    return shared
    .click('#edit-sort-bef-combine option[value="notice_date DESC"]')
    .click('#edit-submit-par-user-enforcement-list')
    .clickLinkByPureText(string)
    .assert.containsText('h1.heading-xlarge .heading-secondary', 'Make a decision')
    .assert.containsText('h1.heading-xlarge', 'Proposed enforcement action(s)')
    .click('#edit-actions-0-primary-authority-status-referred')
    .setValue('#edit-actions-0-referral-notes', 'Some notes about why enforcement action referred')
    .click('#edit-actions-next')
    .click('.form-radio')
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge', 'Enforcement action decision')
    .click('#edit-actions-next')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPureText('See enforcement notifications sent')
    .assert.containsText('.cols-6', string)
    .assert.containsText('.cols-6', 'Referred')
    });
