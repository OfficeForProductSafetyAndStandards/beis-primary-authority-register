const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared()

const faker = require('faker/locale/en_GB')

const title = faker.fake("{{name.prefix}}")
const firstname = faker.fake("{{name.firstName}}")
const lastname = faker.fake("{{name.lastName}}")
const work_number = faker.fake("{{phone.phoneNumber}}")
const mobile_number = faker.fake("{{phone.phoneNumber}}")
const email = faker.fake("{{internet.email}}")

const partnership_information = faker.fake("{{lorem.paragraph}}")

const postcode = faker.fake("{{address.zipCode}}")
const city = faker.fake("{{address.city}}")
const streetaddress1 = faker.fake("{{address.streetName}}")
const streetaddress2 = faker.fake("{{address.state}}")
const county = faker.fake("{{address.county}}")

When('I apply for a direct partnership', function () {
    return shared
        .clickLinkByPureText('Apply for a new partnership')
        .click('#edit-cancel')
        .clickLinkByPureText('Apply for a new partnership')
        .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//div[text()="Lower East Side Borough Council"]')
        .assert.containsText('h1.heading-xlarge', 'What kind of partnership are you applying for?')
        .click('#edit-application-type-direct')
        .click('#edit-next')
        .assert.containsText('h1.heading-xlarge', 'Declaration for a direct partnership application')
        .click('#edit-confirm')
        .click('#edit-next')
})
When('I apply for a coordinated partnership', function () {
    return shared
        .clickLinkByPureText('Apply for a new partnership')
        .click('#edit-cancel')
        .clickLinkByPureText('Apply for a new partnership')
        .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//div[text()="Lower East Side Borough Council"]')
        .assert.containsText('h1.heading-xlarge', 'What kind of partnership are you applying for?')
        .click('#edit-application-type-coordinated')
        .click('#edit-next')
        .assert.containsText('h1.heading-xlarge', 'Declaration for a coordinated partnership application')
        .click('#edit-confirm')
        .click('#edit-next')
})
When('I enter information about the partnership', function () {
    return shared
        .assert.containsText('h1.heading-xlarge',' Information about the new partnership')
        .click('#edit-next')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'You must enter some information about this partnership')
        .clearValue('#edit-about-partnership')
        .setValue('#edit-about-partnership', partnership_information)
        .click('#edit-next')
})
When('I enter the business name {string}', function (organisation) {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Who are you in partnership with?')
        .click('#edit-next')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'You must enter the organisation\'s name')
        .clearValue('#edit-name')
        .setValue('#edit-name', organisation)
        .click('#edit-next')
})
When('I enter the business address', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Add member organisation address')
        .clearValue('#edit-postcode')
        .clearValue('#edit-address-line1')
        .clearValue('#edit-address-line2')
        .clearValue('#edit-town-city')
        .clearValue('#edit-county')
        .click('#edit-next')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'You must enter a valid postcode')
        .assert.containsText('.error-summary', 'You must enter the town or city for this address')
        .assert.containsText('.error-summary', 'You must enter the first line of your address')
        .setValue('#edit-postcode', postcode)
        .setValue('#edit-address-line1', streetaddress1)
        .setValue('#edit-town-city', city)
        .setValue('#edit-county', county)
        .click('#edit-country-code option[value="GB"]')
        .click('#edit-nation option[value="GB-ENG"]')
        .click('#edit-next')
});
When('I enter the contact details for the business', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Add a contact for the organisation')
        .clearValue('#edit-salutation')
        .clearValue('#edit-first-name')
        .clearValue('#edit-last-name')
        .clearValue('#edit-work-phone')
        .clearValue('#edit-mobile-phone')
        .clearValue('#edit-email')
        .click('#edit-next')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'You must enter the first name for this contact')
        .assert.containsText('.error-summary', 'You must enter the last name for this contact')
        .assert.containsText('.error-summary', 'You must enter the work phone number for this contact')
        .assert.containsText('.error-summary', 'You must enter the email address for this contact')
        .setValue('#edit-salutation', title)
        .setValue('#edit-first-name', firstname)
        .setValue('#edit-last-name', lastname)
        .setValue('#edit-work-phone', work_number)
        .setValue('#edit-mobile-phone', mobile_number)
        .setValue('#edit-email', email)
        .click('#edit-next')
});
When('I invite the business contact', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Invite the business')
        .assert.containsText('#edit-recipient', email)
        .assert.containsText('#edit-body', 'Dear '+firstname)
        .assert.containsText('#edit-body', '[invite:invite-accept-link]')
        .click('#edit-next')
})
When('I review the partnership application between {string} and {string}', function (authority, organisation) {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Check partnership information')
        .click('#edit-save')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'Please confirm you have read the terms & conditions')
        .assert.containsText('#edit-about-partnership', partnership_information)
        .assert.containsText('#edit-organisation-name', organisation)
        .assert.containsText('#edit-authority-name', authority)
        .assert.containsText('#edit-organisation-contact', email)
        .assert.containsText('#edit-organisation-registered-address', postcode)
        .click('#edit-terms-authority-agreed')
        .click('#edit-save')
})

// Starting point: After logging in a PA user
When('I complete valid direct partnership application details', function () {
  return shared
  .clickLinkByPureText('Apply for a new partnership')
  .click('#edit-cancel')
  .clickLinkByPureText('Apply for a new partnership')
  .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//div[text()="City Enforcement Squad"]')
  .click('#edit-application-type-direct')
  .click('#edit-next')
  .click('#edit-next')
  .waitForElementVisible('.error-summary', 1000)
  .assert.containsText('.error-summary', 'Please confirm that all conditions for a new partnership have been met')
  .click('#edit-edit-confirm')
  .click('#edit-next')
  .click('#edit-next')
  .waitForElementVisible('.error-summary', 1000)
  .assert.containsText('.error-summary', 'Please confirm whether the organisation has been notified that any existing local authorities will continue to regulate it')
  .click('#edit-business-notified-2')
  .click('#edit-next')
  .assert.containsText('#par-partnership-about','Use this section to give a brief overview of the partnership')
  .setValue('#edit-about-partnership', 'About the partnership detail')
  .click('#edit-next')
});

// Starting point: After entering in about partnership details
Given('I complete valid organisation details for direct partnership {string}', function (partnershipname) {
  console.log(title,' | ' + firstname,' | '+lastname,' | '+postcode,' | '+city,' | '+streetaddress1,' | '+county)
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
  .setValue( '#edit-salutation',title)
  .setValue( '#edit-first-name',firstname)
  .setValue( '#edit-last-name',lastname)
  .setValue( '#edit-work-phone','999999999')
  .setValue( '#edit-mobile-phone','1111111111111')
  .setValue( '#edit-work-phone','02079999999')
  .setValue( '#edit-mobile-phone','078659999999')
  .setValue( '#edit-email','par_business@example.com')
  .click('#edit-preferred-contact-communication-mobile')
  .setValue( '#edit-notes','Some additional notes')
  .click('#edit-next')
});

// Starting point: After comopleting and submitting the organisation details
When('I complete review and submit valid direct partnership application', function () {
  return client
    .assert.containsText('h1.heading-xlarge .heading-secondary','New partnership application')
    .assert.containsText('h1.heading-xlarge','Check partnership information')
    .click('#edit-partnership-info-agreed-authority')
    .click('#edit-save')
});

// Starting point: After review and submission complete
When('the direct partnership creation email template is correct', function () {
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
