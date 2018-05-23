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
    // .assert.containsText('#par-enforcement-notice-approve', 'Type of enforcement notice')
    // .assert.containsText('#par-enforcement-notice-approve', 'Proposed')
    // .click('#edit-next')
    // .click('.button')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Response to notification of enforcement action sent')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPureText('See enforcement notices')
    .assert.containsText('.cols-5', string)
    .assert.containsText('.cols-5', 'Approved')
});

// Starting point: enforcement notifications received dashboard
When('I successfully block enforcement notice {string}', function (string) {
    return shared
    .clickLinkByPureText(string)
    .click('#edit-par-component-enforcement-action-review-0-primary-authority-status-blocked')
    .setValue('#edit-par-component-enforcement-action-review-0-primary-authority-notes', 'Some notes about why enforcement action blocked')
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Review')
    .click('#edit-save')
    // .assert.containsText('#par-enforcement-notice-approve', 'Type of enforcement notice')
    // .assert.containsText('#par-enforcement-notice-approve', 'Proposed')
    // .click('#edit-next')
    // .click('.button')
    .assert.containsText('h1.heading-xlarge', 'Respond to notice of enforcement action')
    .assert.containsText('h1.heading-xlarge', 'Response to notification of enforcement action sent')
    .clickLinkByPureText('Dashboard')
    .clickLinkByPureText('See enforcement notices')
    .assert.containsText('.cols-5', string)
    .assert.containsText('.cols-5', 'Approved')
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
    .clickLinkByPureText('See enforcement notices')
    .assert.containsText('.cols-6', string)
    .assert.containsText('.cols-6', 'Referred')
    });
