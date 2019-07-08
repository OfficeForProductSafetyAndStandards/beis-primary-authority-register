const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

When('I update the registered address for organisation', function () {
  return shared
  .clickLinkByPureText('edit address')
  .clearValue('#edit-county')
  .clearValue('#edit-town-city')
  .clearValue('#edit-address-line2')
  .clearValue('#edit-address-line1')
  .clearValue('#edit-postcode')
  .click('#edit-save')
  .waitForElementVisible('.error-summary', 1000)
  .setValue('#edit-postcode','SE16 4NX')
  .setValue('#edit-address-line1','1 Change St')
  .setValue('#edit-address-line2','New Change')
  .setValue('#edit-town-city','London')
  .setValue('#edit-county','London')
  .click('#edit-country-code option[value="GB"]')
  .click('#edit-nation option[value="GB-ENG"]')
  .click('#edit-save')
  .assert.containsText('#edit-registered-address', '1 Change St')
  .assert.containsText('#edit-registered-address', 'New Change')
  .assert.containsText('#edit-registered-address', 'London')
  .assert.containsText('#edit-registered-address', 'SE16 4NX')
});

When('I update about the organisation', function () {
  return shared
    .clickLinkByPureText('edit about the organisation')
    .clearValue('#edit-about-business')
    .assert.containsText('h1.heading-xlarge .heading-secondary', 'Primary Authority partnership information')
    .assert.containsText('h1.heading-xlarge', 'Information about the organisation')
    .setValue('#edit-about-business','Change to the about organisation details section')
    .click('#edit-save')
    .assert.containsText('#edit-about-business', 'Change to the about organisation details section')
});

When('I edit about the partnership', function () {
  return shared
    .clickLinkByPureText('edit about the partnership')
    .clearValue('#edit-about-partnership')
    .assert.containsText('h1.heading-xlarge .heading-secondary', 'Primary Authority partnership information')
    .assert.containsText('h1.heading-xlarge', 'Information about the new partnership')
    .setValue('#edit-about-partnership','Change to the about partnership details section')
    .click('#edit-save')
    .assert.containsText('#edit-about-partnership', 'Change to the about partnership details section')
});

When('I update the SIC code', function () {
  return shared
      .clickLinkByPureText('add another sic code')
      .click('#edit-sic-code option[value="38"]')
      .click('#edit-save')
      .assert.containsText('#edit-sic-codes', 'Social care activities without accommodation')
    });

When('I update the number of employees', function () {
  return shared
    .clickLinkByPureText('edit number of employees')
    .click('#edit-employees-band option[value="250"]')
    .click('#edit-save')
    .assert.containsText('#edit-employee-no', '50 to 249')
  });

When('I add and subsequently edit a trading name', function () {
    return shared
      .clickLinkByPureText('add another trading name')
      .assert.containsText('h1.heading-xlarge', 'Add a trading name for your organisation')
      .clearValue('#edit-trading-name')
      .setValue('#edit-trading-name', 'Different Trading Name')
      .click('#edit-save')
      .assert.containsText('#edit-trading-names', 'Different Trading Name')
      .clickLinkByPureText('edit trading name')
      .clearValue('#edit-trading-name')
      .assert.containsText('h1.heading-xlarge', 'Edit trading name for your organisation')
      .setValue('#edit-trading-name', 'Change To Different Trading Name')
      .click('#edit-save')
      .assert.containsText('#edit-trading-names', 'Change To Different Trading Name')
  });

  When('I edit the main authority contact', function () {
    return shared
      .clickLinkByPureText('edit big bird')
      .clearValue('#edit-salutation')
      .clearValue('#edit-first-name')
      .clearValue('#edit-last-name')
      .clearValue('#edit-work-phone')
      .clearValue('#edit-mobile-phone')
      .setValue('#edit-salutation','Mrs')
      .setValue('#edit-first-name','Helen')
      .setValue('#edit-last-name','Brittas')
      .setValue('#edit-work-phone','02078886663')
      .setValue('#edit-mobile-phone','07965465726')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Review contact information')
      .assert.containsText('#edit-name', 'Mrs Helen Brittas')
      .assert.containsText('#edit-work-phone', '02078886663')
      .assert.containsText('#edit-mobile-phone', '07965465726')
      .click('#edit-next')
});

When('I upload a file to the partnership advice section', function () {
  return shared
    .clickLinkByPureText('See all Advice')
    .clickLinkByPureText('Upload advice')
    .assert.containsText('h1.heading-xlarge', 'Uploading advice documents declaration')
    .click('#edit-declaration')
    .click('#edit-save')
    .assert.containsText('h3.heading-medium', 'How to upload Primary Authority Advice to Local Authorities')
    .setValue('#edit-files-upload', __dirname + '/files/test.png')
    .click('#edit-upload')
    .waitForElementVisible('#par-partnership-advice-add', 2000)
    .assert.containsText('h1.heading-xlarge', 'Edit advice details')
    .setValue('#edit-advice-title','Advice upload test')
    .setValue('#edit-notes','This is a summary description for this test advice, the contents of this description should be searchable.')
    .click('#edit-regulatory-functions--wrapper > div > label')
    .click('#edit-advice-type-business-advice')
    .click('#edit-save')
    // @TODO Drupal renumbers existing files, had to change from:
    // .assert.containsText('.table-scroll-wrapper', 'Download test.png')
    .assert.containsText('.table-scroll-wrapper', 'Advice upload test')
    // shared.assert.containsText('.table-scroll-wrapper', 'Primary Authority advice for the organisation covering: ' + result.value)
    .clickLinkByPureText('Done')
});

When('I add and subsequently edit a organisation contact', function () {
return shared
      .clickLinkByPureText('add another organisation contact')
      .clearValue('#edit-salutation')
      .clearValue('#edit-first-name')
      .clearValue('#edit-last-name')
      .clearValue('#edit-work-phone')
      .clearValue('#edit-mobile-phone')
      .clearValue('#edit-email')
      .setValue('#edit-salutation', 'Mrs')
      .setValue('#edit-first-name', 'Carol')
      .setValue('#edit-last-name', 'Parkinson')
      .setValue('#edit-work-phone', '02086008000')
      .setValue('#edit-mobile-phone', '07339121212')
      .setValue('#edit-email', 'another.contact@example.com')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Invite the person to create an account')
      .assert.containsText('#edit-sender', 'par_business@example.com')
      .assert.containsText('#edit-recipient', 'another.contact@example.com')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Review contact information')
      .assert.containsText('#edit-name', 'Mrs Carol Parkinson')
      .assert.containsText('#edit-work-phone', '02086008000')
      .assert.containsText('#edit-mobile-phone', '07339121212')
      .click('#edit-next')
      .clickLinkByPureText('edit carol parkinson')
      .clearValue('#edit-salutation')
      .clearValue('#edit-first-name')
      .clearValue('#edit-last-name')
      .clearValue('#edit-work-phone')
      .clearValue('#edit-mobile-phone')
      .setValue('#edit-salutation', 'Ms')
      .setValue('#edit-first-name', 'Laura')
      .setValue('#edit-last-name', 'Lansing')
      .setValue('#edit-work-phone', '1234865432')
      .setValue('#edit-mobile-phone', '07877943768')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Review contact information')
      .assert.containsText('#edit-name', 'Ms Laura Lansing')
      .assert.containsText('#edit-work-phone', '1234865432')
      .assert.containsText('#edit-mobile-phone', '07877943768')
      .click('#edit-next')
      .clickLinkByPureText('edit carol parkinson')
      .clearValue('#edit-email')
      .setValue('#edit-email', 'laura.lansing@example.com')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Invite the person to create an account')
      .assert.containsText('#edit-sender', 'par_business@example.com')
      .assert.containsText('#edit-recipient', 'another.contact@example.com')
      .click('#edit-next')
      .assert.containsText('h1.heading-xlarge', 'Review contact information')
      .assert.containsText('#edit-name', 'Ms Laura Lansing')
      .assert.containsText('#edit-email', 'another.contact@example.com')
  });
