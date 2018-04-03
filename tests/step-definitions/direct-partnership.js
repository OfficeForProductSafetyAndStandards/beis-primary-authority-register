const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

When('I complete valid direct partnership application details', function () {
  return shared
  .clickLinkByPureText('Apply for a new partnership')
  .click('#edit-cancel')
  .clickLinkByPureText('Apply for a new partnership')
  .click('input[name="par_data_authority_id"]')
  .click('#edit-next')
  // .chooseAuthorityIfOptionPresent('selector','input[type="radio"]')
  .click('#edit-application-type-direct')
  .click('#edit-next')
  .click('#edit-business-eligible-for-partnership')
  .click('#edit-local-authority-suitable-for-nomination')
  .click('#edit-written-summary-agreed')
  .click('#edit-next')
  .waitForElementVisible('.error-summary', 1000)
  .click('#edit-terms-organisation-agreed')
  .click('#edit-business-regulated-by-one-authority-1')
  .click('#edit-next')
  .waitForElementVisible('.error-summary', 1000)
  .assert.containsText('#par-partnership-application-authority-checklist', 'Is this your local authority?')
  .click('#edit-business-regulated-by-one-authority-1')
  .click('#edit-is-local-authority-1')
  .click('#edit-next')
  .assert.containsText('#par-partnership-about','Use this section to give a brief overview of the partnership')
  .setValue('#edit-about-partnership', 'About the partnership detail')
  .click('#edit-next')
});
  
Given('I complete valid organisation details for direct partnership {string}', function (partnershipname) {
  return client
  .setValue('#edit-organisation-name',partnershipname)
  .click('#edit-next')
  .click('#edit-next')
  .clearValue('#edit-postcode')
  .clearValue('#edit-address-line1')
  .clearValue('#edit-address-line2')
  .clearValue('#edit-town-city')
  .clearValue('#edit-county')
  .setValue( '#edit-postcode','SE16 4NX')
  .setValue( '#edit-address-line1','1 Change St')
  .setValue( '#edit-address-line2','New Change')
  .setValue( '#edit-town-city','London')
  .setValue( '#edit-county','London')
  .click('#edit-country-code option[value="GB"]')
  .click('#edit-nation option[value="GB-ENG"]')
  .click('#edit-next')
  //  MAIN CONTACT
  .clearValue( '#edit-salutation')
  .clearValue( '#edit-first-name')
  .clearValue( '#edit-last-name')
  .clearValue( '#edit-work-phone')
  .clearValue( '#edit-mobile-phone')
  .clearValue( '#edit-work-phone')
  .clearValue( '#edit-mobile-phone')
  .clearValue( '#edit-email')
  .setValue( '#edit-salutation','Mr')
  .setValue( '#edit-first-name','Fozzie')
  .setValue( '#edit-last-name','Bear')
  .setValue( '#edit-work-phone','999999999')
  .setValue( '#edit-mobile-phone','1111111111111')
  .setValue( '#edit-work-phone','02079999999')
  .setValue( '#edit-mobile-phone','078659999999')
  .setValue( '#edit-email','par_business@example.com')
  .click('#edit-preferred-contact-communication-mobile')
  .setValue( '#edit-notes','Some additional notes')
  .click('#edit-next')
});

When('I complete review of the valid direct partnership application', function () {
  return client
    .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
    .assert.containsText('h1.heading-xlarge','Review the partnership summary information below')
    .click('#edit-partnership-info-agreed-authority')
    .click('#edit-save')
}); 

When('I check the email confirmations have processed correctly', function () {
 return shared
  .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
  .assert.containsText('h1.heading-xlarge','Notify user of partnership invitation')
  .waitForElementVisible('input[value=\"Invitation to join the Primary Authority Register\"]', 1000)
  .click('#edit-next')
  .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
  .assert.containsText('h1.heading-xlarge','Notification sent')
  .assert.containsText('#block-par-theme-content','Mr Fozzie Bear will receive an email with a link to register/login to the PAR website')
  .clickLinkByPureText('Done')
  .assert.containsText('h1.heading-xlarge','Primary Authority Register')
}); 