const { client } = require('nightwatch-cucumber');
const { Given, Then, When } = require('cucumber');
const shared = client.page.shared();

When('I create new valid enforcement notication {string} for organisation {string}', function (string,string2) {
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
  .assert.containsText('#par-enforce-organisation','Choose the member to enforce')
  .click('.form-radio')
  .click('#edit-next')
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
  .click('#edit-type-proposed')
  .click('#edit-next')
  .setValue('#edit-title', 'Enforcement notice ' + string)
  .click('.form-radio')
  .setValue('#edit-details', 'Some details about the enforcement notice' + string)
  .click('#edit-next')
  .assert.containsText('#par-enforce-organisation','action summary enforcement notice')
  .assert.containsText('#par-enforce-organisation', string)
  .assert.containsText('#par-enforce-organisation','Some details about the enforcement notice' + string)
  .assert.containsText('#par-enforce-organisation','Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action')
  .assert.containsText('#par-enforce-organisation','You will be notified by email of the outcome of this notification')
  .click('#edit-save')
  .assert.containsText('h1.heading-xlarge','Primary Authority Register')
})
  
When('I check that EO can see valid enforcement notification {string}', function (string) {
  // CHECK RECEIVED ENFORCEMENT NOTIFICATIONS
  shared
  .url(client.launch_url + '/user/login')
  .setValue('#edit-name', 'par_authority@example.com')
  .setValue('#edit-pass', 'TestPassword')
  .click('#edit-submit')
  .assert.containsText('#block-par-theme-account-menu', 'Log out')
  .clickLinkByPureText('See enforcement notifications received')
  .assert.containsText('.table-scroll-wrapper', string)
})