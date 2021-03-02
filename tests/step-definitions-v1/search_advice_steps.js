const {client} = require('nightwatch-cucumber')
const {Given, Then, When} = require('cucumber')
const shared = client.page.shared()


Given('I search for an active partnership {string}', function (string) {
  return shared
      .clickLinkByPureText('Dashboard')
      .clickLinkByPartialText('Search for a partnership')
      .setValue('#edit-keywords', string)
      .click('#edit-submit-partnership-search')
      .clickLinkByPartialText(string)
});

Then('I search for active advice by the title {string}', function (string) {
  return shared
      .setValue('#edit-keywords', string)
      .click('#edit-submit-advice-lists')
      .clickLinkByPureText(string)
});

Given('I navigate to a partnership confirmed by the authority {string}', function (string) {
    return shared
        .clickLinkByPureText('Dashboard')
        .clickLinkByPartialText('Manage partnerships')
        .setValue('#edit-keywords', string)
        .click('#edit-partnership-status-1 option[value="confirmed_authority"]')
        .click('#edit-submit-par-user-partnerships')
        .clickLinkByPureText(string)
});
