const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

// Starting point: enforcement notifications received dashboard
When('I successfully approve enforcement notice {string}', function (string) {
return shared
    .clickLinkByPureText(string)
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Proposed enforcement action(s)')
    .assert.containsText('#par-enforcement-notice-approve', 'Enforcement officer')
    .assert.containsText('#par-enforcement-notice-approve', 'par_enforcement_officer@example.com')
    .assert.containsText('#par-enforcement-notice-approve', 'Enforcing authority')
    .assert.containsText('#par-enforcement-notice-approve', 'Enforced organisation')
    .assert.containsText('#par-enforcement-notice-approve', 'Hooper\'s Store')
    .assert.containsText('#par-enforcement-notice-approve', 'Primary authority')
    .assert.containsText('#par-enforcement-notice-approve', 'par_authority@example.com')
    .click('#edit-par-component-enforcement-action-review-0-primary-authority-status-approved')
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Review')
    .click('#edit-save')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Response to notification of enforcement action sent')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPartialText('See your enforcement notices')
    .assert.containsText('.par-user-enforcement-list', string)
    .assert.containsText('.par-user-enforcement-list', 'Approved')
});

// Starting point: enforcement notifications received dashboard
When('I successfully block enforcement notice {string}', function (enforcement_notice) {
    return shared
    .clickLinkByPureText(enforcement_notice)
    .click('#edit-par-component-enforcement-action-review-0-primary-authority-status-blocked')
    .setValue('#edit-par-component-enforcement-action-review-0-primary-authority-notes', 'Some notes about why enforcement action blocked')
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Review')
    .click('#edit-save')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Response to notification of enforcement action sent')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPartialText('See your enforcement notices')
    .assert.containsText('.par-user-enforcement-list', enforcement_notice)
    .assert.containsText('.par-user-enforcement-list', 'Approved')
});

// Starting point: enforcement notifications received dashboard
When('I successfully refer enforcement notice {string} to {string}', function (enforcement_notice, new_authority) {
    return shared
    .clickLinkByPureText(enforcement_notice)
    .click('#edit-par-component-enforcement-action-review-0-primary-authority-status-referred')
    .setValue('#edit-par-component-enforcement-action-review-0-referral-notes', 'Some notes about why the enforcement action has been referred')
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge', 'Refer Enforcement Notice')
    .click('#edit-par-component-enforcement-action-refer-0 .multiple-choice .form-radio:nth-child(1)')
    .click('#edit-save')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Review')
    .click('#edit-save')
    .assert.containsText('h1.heading-xlarge', 'Response to notification of enforcement action sent')
    .clickLinkByPureText('Done')
});
