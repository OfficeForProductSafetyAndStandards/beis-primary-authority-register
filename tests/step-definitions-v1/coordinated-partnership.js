const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();
var title = 'Dr'
var firstname = 'Jill'
var lastname = 'Langworthy'
var postcode = 'SE19JJ'
var city = 'Ministerworth'
var streetaddress1 = '66 High St'
var county = 'Gloucestershire'

When('I complete valid coordinated partnership application details', function () {
  return shared
  .clickLinkByPureText('Apply for a new partnership')
  .click('#edit-cancel')
  .clickLinkByPureText('Apply for a new partnership')
  .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//div[text()="City Enforcement Squad"]')
  .click('#edit-application-type-coordinated')
  .click('#edit-next')
  .click('#edit-next')
  .waitForElementVisible('.govuk-error-summary', 1000)
  .assert.containsText('.govuk-error-summary', 'Please confirm that all conditions for a new partnership have been met')
  .click('#edit-edit-confirm')
  .click('#edit-next')
  .assert.containsText('h1.heading-xlarge','Information about the new partnership')
  .setValue('#edit-about-partnership', 'About the partnership detail')
  .click('#edit-next')
});

Given('I complete valid organisation details for coordinated partnership {string}', function (partnershipname) {
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
  .clearValue( '#edit-notes')
  .setValue( '#edit-salutation',title)
  .setValue( '#edit-first-name',firstname)
  .setValue( '#edit-last-name',lastname)
  .setValue( '#edit-work-phone','999999999')
  .setValue( '#edit-mobile-phone','1111111111111')
  .setValue( '#edit-work-phone','02079999999')
  .setValue( '#edit-mobile-phone','078659999999')
  .setValue( '#edit-email','par_coordinator@example.com')
  .click('#edit-preferred-contact-communication-mobile')
  .setValue( '#edit-notes','Some additional notes about the updated contact.')
  .click('#edit-next')
});

When('I complete review and submit valid coordinated partnership application', function () {
  return client
    .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
    .assert.containsText('h1.heading-xlarge','Check partnership information')
    .click('#edit-partnership-info-agreed-authority')
    .click('#edit-save')
});

When('I complete the business contact details', function () {
  return client
  .clearValue( '#edit-salutation')
  .clearValue( '#edit-first-name')
  .clearValue( '#edit-last-name')
  .clearValue( '#edit-work-phone')
  .clearValue( '#edit-mobile-phone')
  .clearValue( '#edit-work-phone')
  .clearValue( '#edit-mobile-phone')
  .clearValue( '#edit-email')
  .setValue( '#edit-salutation',title)
  .setValue( '#edit-first-name',firstname)
  .setValue( '#edit-last-name',lastname)
  .setValue( '#edit-work-phone','999999999')
  .setValue( '#edit-mobile-phone','1111111111111')
  .setValue( '#edit-work-phone','02079999999')
  .setValue( '#edit-mobile-phone','078659999999')
  .setValue( '#edit-email','par_coordinator@example.com')
  .click('#edit-preferred-contact-communication-mobile')
  .setValue( '#edit-notes','Some additional notes')
  .click('#edit-next')
});

When('the coordinated partnership creation email template is correct', function () {
 return shared
  .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
  .assert.containsText('h1.heading-xlarge','Notify user of partnership invitation')
  .waitForElementVisible('input[value=\"Invitation to join the Primary Authority Register\"]', 1000)
  .click('#edit-next')
  .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
  .assert.containsText('h1.heading-xlarge','Notification sent')
  .assert.containsText('#block-par-theme-content', title + ' ' + firstname + ' ' + lastname + ' will receive an email with a link to register/login to the PAR website')
  .clickLinkByPureText('Done')
  .assert.containsText('h1.heading-xlarge','Primary Authority Register')
});

When('I complete the organisation registered address for coordinated partnership', function () {
  return shared
  .setValue('#edit-postcode', '')
  .setValue('#edit-address-line1', '')
  .setValue('#edit-address-line2', '')
  .setValue('#edit-town-city', '')
  .setValue('#edit-county', '')
  .clearValue('#edit-postcode')
  .clearValue('#edit-address-line1')
  .clearValue('#edit-address-line2')
  .clearValue('#edit-town-city')
  .clearValue('#edit-county')
  .click('#edit-next')
  .waitForElementVisible('.govuk-error-summary', 2000)
  .assert.containsText('h1.heading-xlarge','Confirm the primary address details')
  .setValue('#edit-postcode','SE16 4NX')
  .setValue('#edit-address-line1','1 High St')
  .setValue('#edit-address-line2','Southwark')
  .setValue('#edit-town-city','London')
  .setValue('#edit-county','London')
  .click('#edit-country-code option[value="GB"]')
  .click('#edit-nation option[value="GB-ENG"]')
  .click('#edit-next')
});
