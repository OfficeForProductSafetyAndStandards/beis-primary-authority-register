const { client } = require('nightwatch-cucumber');
const { Given, Then, When } = require('cucumber');
const shared = client.page.shared();

// Rules for writing new tests:
// 1. Small: Use *small*, simple, single purpose, almost atomic steps. No more than 10 functions.
// 2. Single purpose: Tests should verify one thing only!
// 3. Assert: There should be an assertion in every step ton confirm it passed.
// 4. Abstraction: Repeatable user interaction should be framed within specific test functions.
// 5. Independent: Test scenarios should not rely on data from another.
// 6. Complexity: Create complex tests from simple steps, allow simple tests to run separately and run first.

// Search for a partnership.
When('I search for a partnership between {string} and {string}', function (authority, organisation) {
    var partnership = "Partnership between " + authority + " and " + organisation;
    return shared
        .clickDashboardLink()
        .clickLinkByPureText('Search for a partnership')
        .assert.containsText('h1.heading-xlarge', 'Search for a partnership')
        .setValue('#edit-keywords', organisation)
        .click('#edit-submit-partnership-search')
        .clickLinkByPureText(partnership)
        .assert.containsText('h1.heading-xlarge', organisation)
        .assert.containsText('h2.heading-large.authority-name', authority)
});
// Raise an enforcement notice.
When('I raise a new enforcement against a partnership', function () {
    return shared
        .clickLinkByPureText('Send a notification of a proposed enforcement action')
        .assert.containsText('h1.heading-xlarge','Have you discussed this issue with the Primary Authority?')
        .click('#edit-next')
        .assert.containsText('h1.heading-xlarge','Contact details for enforcement officer')
        .click('#edit-next')
});
When('I choose a coordinated member to enforce', function () {
    return shared
        .click('input[name="par_data_organisation_id"]:nth-child(1)')
        .assert.containsText('h1.heading-xlarge','Enforce member')
        .click('#edit-next')
});
When('I choose an existing legal entity to enforce', function () {
    return shared
        .click('input[name="legal_entities_select"]:nth-child(1)')
        .assert.containsText('h1.heading-xlarge','Enforce legal entity')
        .click('#edit-next')
});
When('I enter a legal entity to enforce {string}', function (legal_entity) {
    return shared
        .clearValue('#edit-alternative-legal-entity')
        .setValue('#edit-alternative-legal-entity', legal_entity)
        .assert.containsText('h1.heading-xlarge','Enforce legal entity')
        .click('#edit-next')
});
When('I enter the details of a proposed enforcement', function () {
    return shared
        .clearValue('#edit-summary')
        .setValue('#edit-summary', 'Summary for a test enforcement notice. This enforcement notice is proposed.')
        .assert.containsText('h1.heading-xlarge','Enforcement details')
        .click('#edit-next')
});
When('I add an enforcement action {string}', function (title) {
    return shared
        .clearValue('#edit-par-component-enforcement-action-0-title')
        .setValue('#edit-par-component-enforcement-action-0-title', title)
        .click('input[name="par_component_enforcement_action[0][regulatory_function]"]')
        .clearValue('#edit-par-component-enforcement-action-0-details')
        .setValue('#edit-par-component-enforcement-action-0-details', 'Enforcement action details of infringement for the primary enforcement action.')
        .assert.containsText('h1.heading-xlarge','Add an action to the enforcement notice')
        .click('#edit-next')
});
When('I add multiple enforcement actions {string}, {string}', function (title_1, title_2) {
    return shared
        .clearValue('#edit-par-component-enforcement-action-0-title')
        .setValue('#edit-par-component-enforcement-action-0-title', title_1)
        .click('input[name="par_component_enforcement_action[0][regulatory_function]"]')
        .clearValue('#edit-par-component-enforcement-action-0-details')
        .setValue('#edit-par-component-enforcement-action-0-details', 'Enforcement action details of infringement for the primary enforcement action.')
        .assert.containsText('h1.heading-xlarge','Add an action to the enforcement notice')
        .click('#edit-add-another')
        .clearValue('#edit-par-component-enforcement-action-1-title')
        .setValue('#edit-par-component-enforcement-action-1-title', title_2)
        .click('input[name="par_component_enforcement_action[1][regulatory_function]"]')
        .clearValue('#edit-par-component-enforcement-action-1-details')
        .setValue('#edit-par-component-enforcement-action-1-details', 'Enforcement action details of infringement for the primary enforcement action.')
        .assert.containsText('h1.heading-xlarge','Add an action to the enforcement notice')
        .click('#edit-next')
});
When('I review the enforcement notice', function () {
    return shared
        .assert.containsText('h1.heading-xlarge','Review the enforcement notice')
        .click('#edit-next')
        .assert.containsText('h1.heading-xlarge','Enforcement notice sent')
        .click('.button')
});



// @deprecated
When('I create new valid enforcement notification {string} for partnership {string} against organisation {string}', function (string, link, search) {
  return shared
    .clickLinkByPureText('Search for a partnership')
    .setValue('#edit-keywords',search)
    .click('#edit-submit-partnership-search')
    .clickLinkByPureText(link)
    // ENFORCEMENT ACTION FORM
    .clickLinkByPureText('Send a notification of a proposed enforcement action')
    .click('#edit-cancel')
    .clickLinkByPureText('Send a notification of a proposed enforcement action')
    .assert.containsText('h1.heading-xlarge','Have you discussed this issue with the Primary Authority?')
    .click('#edit-next')
      // CHOOSE MEMBER
    .chooseMemberIfOptionPresent()
    // ENTER EO DETAILS
    .clearValue('#edit-first-name')
    .click('#edit-next')
    .waitForElementVisible('.error-summary', 1000)
    .setValue('#edit-first-name', 'Colin')
    .click('#edit-next')
    // CHOOSE LEGAL ENTITY
    .click('#edit-next')
    // ENFORCEMENT SUMMARY
    .setValue('#edit-summary', 'action summary enforcement notice')
    .click('#edit-notice-type-proposed')
    .click('#edit-next')
    .setValue('#edit-par-component-enforcement-action-0-title', string)
    .click('.form-radio')
    .setValue('#edit-par-component-enforcement-action-0-details', 'Some details about the enforcement notice' + string)
    .click('#edit-next')
    .assert.containsText('#par-enforcement-notice-raise-review','action summary enforcement notice')
    .assert.containsText('#par-enforcement-notice-raise-review', string)
    .assert.containsText('#par-enforcement-notice-raise-review','action summary enforcement notice')
    .click('#edit-save')
    .assert.containsText('h1.heading-xlarge','Enforcement notice sent')
    .click('.button')
    .assert.containsText('h1.heading-xlarge','Partnership Search')
});

// @deprecated
When('I check that EO can see valid enforcement notification {string}', function (string) {
  // CHECK RECEIVED ENFORCEMENT NOTIFICATIONS
  return shared
  .clickLinkByPureText('Sign out')
  .waitForElementVisible('.button-start', 2000)
  .clickLinkByPureText('Sign in')
  .setValue('#edit-name', 'par_authority@example.com')
  .setValue('#edit-pass', 'TestPassword')
  .click('#edit-submit')
  .assert.containsText('#block-par-theme-account-menu', 'Sign out')
  .clickLinkByPartialText('See your enforcement notices')
  .assert.containsText('.table-scroll-wrapper', string)
})
