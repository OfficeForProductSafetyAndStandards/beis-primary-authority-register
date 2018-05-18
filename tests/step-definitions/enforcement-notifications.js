const { client } = require('nightwatch-cucumber');
const { Given, Then, When } = require('cucumber');
const shared = client.page.shared();

When('I create new valid enforcement notification {string} for organisation {string}', function (string, string2) {
  return shared
    .clickLinkByPureText('Search for a partnership')
    .setValue('#edit-keywords',string2)
    .click('#edit-submit-partnership-search')
    .click('td.views-field.views-field-par-flow-link a')
    // ENFORCEMENT ACTION FORM
    .clickLinkByPureText('Send a notification of a proposed enforcement action')
    .click('#edit-cancel')
    .clickLinkByPureText('Send a notification of a proposed enforcement action')
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
    .click('#edit-next')
    .assert.containsText('h1.heading-xlarge','Enforcement notice sent')
    .click('.button')
    .assert.containsText('h1.heading-xlarge','Partnership Search')
});
  
When('I check that EO can see valid enforcement notification {string}', function (string) {
  // CHECK RECEIVED ENFORCEMENT NOTIFICATIONS
  return shared
  .clickLinkByPureText('Log out')
  .waitForElementVisible('.button-start', 2000)
  .clickLinkByPureText('Log in')
  .setValue('#edit-name', 'par_authority@example.com')
  .setValue('#edit-pass', 'TestPassword')
  .click('#edit-submit')
  .assert.containsText('#block-par-theme-account-menu', 'Log out')
  .clickLinkByPureText('See enforcement notices')
  .assert.containsText('.table-scroll-wrapper', string)
})
