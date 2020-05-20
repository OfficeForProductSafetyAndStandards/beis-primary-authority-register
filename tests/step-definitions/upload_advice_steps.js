const {client} = require('nightwatch-cucumber')
const {Given, Then, When} = require('cucumber')
const shared = client.page.shared()


Given('I navigate to an active partnership {string}', function (string) {
  return shared
      .clickLinkByPureText('Dashboard')
      .clickLinkByPartialText('See your partnerships')
      .setValue('#edit-keywords', string)
      .click('#edit-partnership-status-1 option[value="confirmed_rd"]')
      .click('#edit-submit-par-user-partnerships')
      .clickLinkByPureText(string)
});
Given('I navigate to a partially completed partnership {string}', function (string) {
  return shared
      .clickLinkByPureText('Dashboard')
      .clickLinkByPartialText('See your partnerships')
      .setValue('#edit-keywords', string)
      .click('#edit-partnership-status-1 option[value="confirmed_authority"]')
      .click('#edit-submit-par-user-partnerships')
      .clickLinkByPureText(string)
});

Then('I should see advice list page', function () {
  return shared
      .assert.containsText('h1.heading-xlarge', 'Advice')
});
Then('I should see advice view page has the title {string}', function (string) {
  return shared
      .assert.containsText('h1.heading-xlarge', string)
});

Then('I should not see the link {string}', function (string) {
  return client
      .expect.element('a.flow-link').to.be.not.present

});


Then('I see the link {string}', function (string) {
  return client
      .assert.containsText('a.flow-link', 'Upload advice')
});

Given('I open an active partnership {string}', function (string) {
  return shared
      .goToPartnershipDetailPage(string, string, 'active')
});

Given('I open advice add page', function () {
  return shared
      .clickLinkByPureText('See all Advice')
      .clickLinkByPureText('Upload advice')
});

Then('I able to upload advice document', function () {
  return client
      .setValue('#edit-files-upload', 'files/test.png')
      .click('#edit-upload')
});

Then('I enter the advice title {string}', function (title) {
  return client
      .clearValue('#edit-advice-title')
      .setValue('#edit-advice-title', title)

});
Then('I enter new advice title', function () {
  return client
      .clearValue('#edit-advice-title')
      .setValue('#edit-advice-title', 'Auto-test-NewAdvice-retest')

});

Then('I enter summary of advice', function () {
  return client
      .clearValue('#edit-notes')
      .setValue('#edit-notes', 'Auto-advice-Summary')

});
Then('I enter new summary of advice', function () {
  return client
      .clearValue('#edit-notes')
      .setValue('#edit-notes', 'Auto-advice-Summary-retest')

});

Then('I select advice type {string}', function (string) {
  return client
      .click('#edit-advice-type-' + string)

});

Then('I select {string} regulatory function', function (string) {
  return client
      .useXpath()
      .click('//label[contains(text(),\"' + string + '"\)]')
      .useCss()
});


Then('I select type of advice', function () {
  return client
      .click('#edit-advice-type-business-advice')
});

Then('I filter by partnership status {string}', function (status) {
  return client
      .click('#edit-partnership-status-1 option[value="'+status+'"]')
});

Then('I select regulatory function', function () {
  return client
      .clickCheckboxIfUnselected('#edit-regulatory-functions-13')
});


Then('I see that the advice {string} uploaded successfully', function (advice) {
  return !shared
      .clickLinkByPureText(advice)
      .assert.containsText('.filename', 'test')
});


Then('I see advice updated successfully', function () {
  return shared
      .clickLinkByPureText('Auto-test-NewAdvice-retest')
      .assert.containsText('h1.heading-xlarge', 'Auto-test-NewAdvice-retest')
      .assert.containsText('#content', 'Auto-advice-Summary-retest')
});


When('I click on edit against an advice', function () {
  return shared
      .clickLinkByPureText('Edit')
      .assert.containsText('h1.heading-xlarge', 'Edit advice details')

});

When('I archive the advice {string} with the reason {string}', function (advice, reason) {
  return shared
      .setValue('#edit-keywords', advice)
      .click('#edit-submit-advice-lists')
      .clickLinkByPureText('Archive')
      .assert.containsText('h1.heading-xlarge', 'Are you sure you want to archive this advice?')
      .setValue('#edit-archive-reason', reason)
      .click('#edit-save')

});

When('I remove the advice {string} with the reason {string}', function (advice, reason) {
  return shared
      .setValue('#edit-keywords', advice)
      .click('#edit-submit-advice-lists')
      .clickLinkByPureText('Remove')
      .assert.containsText('h1.heading-xlarge', 'Are you sure you want to remove this advice?')
      .setValue('#edit-remove-reason', reason)
      .click('#edit-next')
});


When('I select file to remove', function () {
  return client
      .click('.form-checkbox')
      .click('#edit-files-remove-button')
      .pause(6000)
});


Then('I should be able to upload new file', function () {

});


Then('I see new file in the advice detail page', function () {
  return client
      .assert.containsText('.file', 'Advice Issued - 01 - Attic Descriptions v2_1')
      .click('#edit-save')

});

When('I enter reason {string}', function (string) {
  return client
      .setValue('#edit-archive-reason', string)
});

Then('I should see the archived advice {string}', function (advice) {
    return client
        .assert.containsText('h1.heading-xlarge', 'Advice')
        .clearValue('#edit-advice-title')
        .setValue('#edit-keywords', advice)
        .click('#edit-submit-advice-lists')
        .assert.containsText('.par-advice-listing','Archived')
});


Then('I should not see the removed advice {string}', function (advice) {
  return client
      .assert.containsText('h1.heading-xlarge', 'Advice')
      .expect.element('.par-advice-listing').text.to.not.contain(advice)
});

