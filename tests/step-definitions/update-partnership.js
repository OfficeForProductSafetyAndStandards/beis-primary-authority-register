const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

When('I edit registered address for organisation', function () {
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

When('I edit about the organisation', function () {
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
  
When('I change the SIC code', function () {
  return shared
      .clickLinkByPureText('add another sic code')
      .click('#edit-sic-code option[value="38"]')
      .click('#edit-save')
      .assert.containsText('#edit-sic-codes', 'Social care activities without accommodation')
    });

When('I change the number of employees', function () {
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
      .clickLinkByPureText('edit authority contact')
      .clearValue('#edit-salutation')
      .clearValue('#edit-first-name')
      .clearValue('#edit-last-name')
      .clearValue('#edit-work-phone')
      .clearValue('#edit-mobile-phone')
      .clearValue('#edit-notes')
      .setValue('#edit-salutation','Mrs')
      .setValue('#edit-first-name','Helen')
      .setValue('#edit-last-name','Brittas')
      .setValue('#edit-work-phone','02078886663')
      .setValue('#edit-mobile-phone','07965465726')
      .click('#edit-preferred-contact-communication-mobile')
      .setValue('#edit-notes','Some additional notes')
      .click('#edit-save')
      .assert.containsText('#edit-authority-contacts', 'Mrs Helen Brittas')
      .assert.containsText('#edit-authority-contacts', '02078886663')
      .assert.containsText('#edit-authority-contacts', '07965465726')
});

When('I upload a file to the partnership advice section', function () {
  return shared
    .clickLinkByPureText('See all Advice')
    .clickLinkByPureText('Upload advice')
    .assert.containsText('h3.heading-medium', 'How to upload Primary Authority Advice to Local Authorities')
    .setValue('input[name=\"files[files][]\"]', __dirname + '/files/test.png') 
    .click('#edit-upload')
    .click('#edit-advice-type-business-advice')
    .click('#edit-regulatory-functions--wrapper > div > div > label')
    .getText('#edit-regulatory-functions--wrapper > div > div > label',function(result){
      shared.click('#edit-save')
      shared.assert.containsText('.table-scroll-wrapper', 'Download test.png')
      shared.assert.containsText('.table-scroll-wrapper', 'Primary Authority advice for the organisation covering: ' + result.value)
      shared.click('.button')
    })
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
      .clearValue('#edit-notes')
      .setValue('#edit-salutation', 'Mrs')
      .setValue('#edit-first-name', 'Carol')
      .setValue('#edit-last-name', 'Parkinson')
      .setValue('#edit-work-phone', '02086008000')
      .setValue('#edit-mobile-phone', '07339121212')
      .setValue('#edit-email', 'another.contact@example.com')
      .setValue('#edit-notes', 'Some additional notes for newly added contact')
      .click('#edit-save')
      .chooseNewPersonIfOptionPresent('input[name="par_data_person_id"]', '#edit-par-data-person-id-new')
      .assert.containsText('.par-partnership-details', '02086008000')
      // .assert.containsText('.par-partnership-details', '07865223222')
      // .clickLinkByPureText('edit Carol Parkinson')
      // .setValue('#edit-salutation', 'Ms')
      // .setValue('#edit-first-name', 'Laura')
      // .setValue('#edit-last-name', 'Lansing')
      // .setValue('#edit-work-phone', '1234865432')
      // .setValue('#edit-mobile-phone', '7877943768')
      // .setValue('#edit-email', 'colin.weatherby@example.com')
      // .click('#edit-preferred-contact-communication-mobile')
      // .setValue('#edit-notes', 'Some additional notes')
      // .click('#edit-save')
      // .assert.containsText('.par-partnership-details', 'Ms Laura Lansing')
      // .assert.containsText('.par-partnership-details', '01234865432')
      // .assert.containsText('.par-partnership-details', '07877943768 (preferred)')
  });
  