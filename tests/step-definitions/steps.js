const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

  Given(/^I open the path "([^"]*)"$/, (locpath) => {
    return client
      .url(client.launch_url+locpath)
      .waitForElementVisible('body', 1000)
      // .assert.urlContains(locpath);
  });

  Then(/^the title is "([^"]*)"$/, (title) => {
    return client.assert.title(title);
  });

  Then(/^the Google search form exists$/, () => {
    return shared.assert.visible('@googleSearchField');
  });

  Then(/^I click on the logout button$/, () => {
    // Debug breakpoint
    // debugger;
    return shared
        .click('@Logout')
        .assert.containsText('#flash','You logged out of the secure area!')
  });

  Then(/^I log out of website$/, () => {
    return shared
        .clickLinkByText('Log out')
        .assert.containsText('#flash','You logged out of the secure area!')
  });

  Then(/^I click the link text "([^"]*)"$/, (linkText) => {
    return shared
        .clickLinkByPureText(linkText)
  });

  Then('I click on the button {string}', function (string) {
    return client.click(string);
  });

  Then('I click on the checkbox {string}', function (string) {
    return client.click(string);
  });

  Then('I click on the radio {string}', function (string) {
    return client.click(string);
  });

  Then('the element {string} contains the text {string}', function (elName, elText) {
    return client.assert.containsText(elName,elText);
  });

  Then('the element {string} does not contain the text {string}', function (string, string2) {
    return client.expect.element(string).text.to.not.contain(string2)
  });

  When('the element {string} is visible', function (string) {
    return client.waitForElementVisible(string, 1000);
  });
 
  Then('the inputfield {string} contains the text {string}', function (string, string2) {
    return client.assert.containsText(string,string2);
  });

  Then('the element {string} contains any text', function (string) {
    return client.waitForElementVisible(elName, 1000);
  });

 When('the element {string} does not exist', function (elName) {
    return client.expect.element('#main').to.not.be.present;
  });

  When('the element {string} does exist', function (elName) {
    return client.assert.waitForElementVisible(elName, 1000);
  });

  When('I select the option with the value {string} for element {string}', function (somevalue, myselectbox) {
    return client.click(myselectbox + ' option[value="'+ somevalue +'"]');
  });

  When('I select the option with the value {string} for element "#edit-par-component-legal-entit  does not exist', function (string, callback) {
    // Write code here that turns the phrase above into concrete actions
    callback(null, 'pending');
  });

  Given('I add {string} to the inputfield {string}', function (string, string2) {
    return client
        .clearValue(string2)
        .setValue(string2, string);
  });

  Given('I am logged in as {string}', function (string) {
    return client
        .url(client.launch_url + '/user/login')
        .setValue('#edit-name', string)
        .setValue('#edit-pass', 'TestPassword')
        .click('#edit-submit')
        .assert.containsText('#block-par-theme-account-menu', 'Log out')
  });

  Then('I clear the inputfield {string}', function (elem) {
    return client
      .clearValue(elem)
  });

  Then('I add {string} random chars of text to field {string}', function (numChars, fieldName) {
    let text = '';
    let n = numChars;
    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ';
    for (var i = 0; i < n; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return client
        .setValue(fieldName, text + 'last text in a long string');
  });

  Then('I upload the file {string} to field {string}', function (string, string2) {
    return client
      .chooseFile(string2, __dirname + '/' + string);
  });

  Then('the element {string} is not empty', function (string) {
    return client.waitForElementVisible(string, 1000);
  });

  Then('the element {string} is empty', function (string) {
    return client.waitForElementVisible(string, 1000);
  });

  When('I open the add members page', function () {
    return client
      .url(function(result) {
        console.log(result)
        var urlMemberAdd = result.value.replace('organisation-details', 'members/add');
        console.log(urlMemberAdd);
        return client.url(urlMemberAdd);    
      })
  });

  When('I click on authority selection if available', function () {
    return shared
            .chooseAuthorityIfOptionPresent('selector','#edit-par-data-authority-id-525')
  });

  When('I click on new organisation option if available', function () {
    return shared
            .chooseNewOrganisationOptionIfPresent('neworg','#edit-par-data-organisation-id-new')
  });

  When('I click new person if suggestions displayed', function () {
    return shared
            .chooseNewPersonIfOptionPresent('newperson','#edit-par-data-person-id-new') 
   });

  When('I run tota11y against the current page', function () {
    client.click('.tota11y-toolbar-toggle');
    var list = ['Headings', 'Contrast', 'Link text', 'Labels', 'Image alt-text'];
    for (var i = 0; i < list.length; i++) {
        browser.click('.tota11y-plugin-title*=' + list[i]);
        if (client.isVisible('.tota11y-info-errors') === true) {
            const errors = browser.getText('.tota11y-info-errors');
            var url = browser.getUrl();
            console.log(url, errors);
            //expect(browser.isVisible('body')).to.equal(true, errors);
        }
    }
    browser.click('.tota11y-toolbar-toggle');
  });
  