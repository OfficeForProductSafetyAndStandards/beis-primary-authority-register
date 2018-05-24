const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared()
var faker = require('faker/locale/en_GB')
var title = faker.fake("{{name.prefix}}")
var firstname = faker.fake("{{name.firstName}}")
var lastname = faker.fake("{{name.lastName}}")
var postcode = faker.fake("{{address.zipCode}}")
var city = faker.fake("{{address.city}}")
var streetaddress1 = faker.fake("{{address.streetName}}")
var county = faker.fake("{{address.county}}")

 When('I go to detail page for partnership with authority {string}', function (authority) {
  return shared
  .clickLinkByPureText('Dashboard')
  .clickLinkByPureText('See your partnerships')
  .setValue('#edit-keywords', authority)
  .click('#edit-submit-par-user-partnerships')
  .clickLinkByPureText(authority)
 });

 // Starting point: logged in as HD user
 When('I successfully revoke a coordinated partnership', function () {
  return shared
    .clickLinkByPureText('Helpdesk')
    .setValue('#edit-keywords','Specialist Cheesemakers Association')
    .click('#edit-revoked option[value="0"]')
    .click('#edit-submit-helpdesk-dashboard')
    .clickLinkByPureText('Revoke partnership')
    .setValue('#edit-revocation-reason','A reason for revoking')
    .click('#edit-next')
    .assert.containsText('#edit-partnership-info','The following partnership has been revoked')
    .click('#edit-done')
    .setValue('#edit-keywords','Specialist Cheesemakers Association')
    .click('#edit-revoked option[value="1"]')
    .click('#edit-submit-helpdesk-dashboard')
    .assert.containsText('.table-scroll-wrapper','Specialist Cheesemakers Association')
    .expect.element('#block-par-theme-content > div > div > div > table > tbody > tr:nth-child(2)').to.not.be.present;
});

// Starting point: at beginning of this application step
When('I complete about the business', function () {
  return shared
  .assert.containsText('h1.heading-xlarge','Confirm the details about the organisation')
  .setValue('#edit-about-business','Some information about organisation details')
  .click('#edit-next')
});

// Starting point: at beginning of this application step
When('I complete the organisation registered address for direct partnership', function () {
  return shared
  .clearValue('#edit-postcode')
  .clearValue('#edit-address-line1')
  .clearValue('#edit-address-line2')
  .clearValue('#edit-town-city')
  .clearValue('#edit-county')
  .assert.containsText('h1.heading-xlarge','Confirm the primary address details')
  .setValue('#edit-postcode','SE16 4NX')
  .setValue('#edit-address-line1','1 High St')
  .setValue('#edit-address-line2','Southwark')
  .setValue('#edit-town-city','London')
  .setValue('#edit-county','London')
  .click('#edit-country-code option[value="GB"]')
  .click('#edit-nation option[value="GB-ENG"]')
  .click('#edit-next')
  .assert.containsText('h1.heading-xlarge','Confirm the primary contact details')
  .click('#edit-next')   
});

// Starting point: at beginning of this application step
When('I complete the SIC codes', function () {
  return shared
  .click('#edit-sic-code option[value="38"]')
   .click('#edit-next')
});

// Starting point: at beginning of this application step
When('I complete the employees', function () {
  return shared
  .click('#edit-employees-band option[value="250"]')
  .click('#edit-next')  
});

// Starting point: at beginning of this application step
When('I complete the trading names', function () {
  return shared
  .assert.containsText('h1.heading-xlarge','Confirm the trading name')
  .setValue('#edit-trading-name','Different Trading Name')
  .click('#edit-next')
});

// Starting point: at beginning of this application step
When('I complete the legal entities', function () {
  return shared
  .assert.containsText('h1.heading-xlarge','Confirm the legal entity')
  .setValue('#edit-par-component-legal-entity-0-registered-name', 'New LLP Company')
  .click('#edit-par-component-legal-entity-0-legal-entity-type option[value="limited_liability_partnership"]')
  .assert.containsText('.form-item-par-component-legal-entity-0-registered-number label','Provide the registration number')
  .setValue('#edit-par-component-legal-entity-0-registered-number', '1234567890')
  .click('#edit-add-another')
  .setValue('#edit-par-component-legal-entity-1-registered-name', 'First Sole Trader')
  .click('#edit-par-component-legal-entity-1-legal-entity-type option[value="sole_trader"]')
  .click('#edit-add-another')
  .setValue('#edit-par-component-legal-entity-2-registered-name', 'Second New LLP Company')
  .click('#edit-par-component-legal-entity-2-legal-entity-type option[value="limited_liability_partnership"]')
  .setValue('#edit-par-component-legal-entity-2-registered-number', '0000000000')
  .click('#edit-add-another')
  .click('#edit-par-component-legal-entity-2-remove')
  .click('#edit-next')
});

// Starting point: at beginning of this application step
When('I review the completions for direct partnership {string}', function (partnershipname) {
  return shared
  .assert.containsText('h1.heading-xlarge','Review the partnership summary information below')
   .assert.containsText('#edit-organisation-name',partnershipname)
   .assert.containsText('#edit-organisation-registered-address','1 High St')
   .assert.containsText('#edit-organisation-registered-address','Southwark')
   .assert.containsText('#edit-organisation-registered-address','London')
   .assert.containsText('#edit-organisation-registered-address','SE16 4NX')
   .assert.containsText('#edit-about-organisation','Some information about organisation details')
   .assert.containsText('#edit-sic-code','Social care activities without accommodation')
   .assert.containsText('#edit-legal-entities','New LLP Company')
   .assert.containsText('#edit-legal-entities','Limited Liability Partnership')
   .assert.containsText('#edit-legal-entities','1234567890')
   .assert.containsText('#edit-legal-entities','First Sole Trader')
});

// Starting point: at beginning of this application step
When('I change the completed legal entities', function () {
  return shared
  .clickLinkByPureText('Change the new legal entities')
  .assert.containsText('h1.heading-xlarge','Confirm the legal entity')
   .click('#edit-par-component-legal-entity-1-remove')
   .setValue('#edit-par-component-legal-entity-0-registered-name','Changed to Public Company')
   .click('#edit-par-component-legal-entity-1-legal-entity-type option[value="public_limited_company"]')
   .assert.containsText('.form-item-par-component-legal-entity-0-registered-number label','Provide the registration number')
   .setValue('#edit-par-component-legal-entity-0-registered-number','55555555558')
   .click('#edit-next')
   .assert.containsText('h1.heading-xlarge','Review the partnership summary information below')
   .assert.containsText('#edit-legal-entities','Changed to Public Company')
   .assert.containsText('#edit-legal-entities','55555555558')
});

// Starting point: at beginning of this application step
When('I change the completed about the organisation', function () {
  return shared
  .clickLinkByPureText('Change the details about this partnership')
  .clearValue('#edit-about-business')
  .setValue('#edit-about-business', 'Change to the information about organisation details')
  .click('#edit-next')   
  .assert.containsText('h1.heading-xlarge','Review the partnership summary information below')
  .assert.containsText('#edit-about-organisation','Change to the information about organisation details')
});

// Starting point: at beginning of this application step
When('I submit final confirmation of completion by organisation {string}', function (partnershipname) {
  return shared
   .click('#edit-save')
   .waitForElementVisible('.error-summary', 2000)
   .click('#edit-partnership-info-agreed-business')
   .click('#edit-save')
   .waitForElementVisible('.error-summary', 1000)
   .click('#edit-terms-organisation-agreed')
   .click('#edit-save')
   .assert.containsText('h1.heading-xlarge','Thank you for completing the application')
   .click('.button')
   .setValue('#edit-keywords',partnershipname)
   .click('#edit-submit-par-user-partnerships')
   .assert.containsText('.table-scroll-wrapper','Confirmed by the Organisation')
 }); 
