const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();

  Given(/^I open the path "([^"]*)"$/, (locpath) => {
    return client
      .url(client.launch_url+locpath)
      .waitForElementVisible('h1', 10000)
      // .assert.urlContains(locpath);
  });

  When('there is {string} occurences of element {string}', function (string, string2) {
    // Write code here that turns the phrase above into concrete actions
    return client.assert.elementCount(string2, string)
  });

  Then('I click the link text {string}', function (string) {
    return shared
        .clickLinkByPureText(string).then(function (){
          return client
            .waitForElementVisible('h1', 3000)
      })
  });

  Then('I click on the button {string}', function (string) {
    return client
        .click(string).then(function (){
          return client
            .waitForElementNotVisible('h1', 3000)
            .waitForElementVisible('h1', 3000)
      })
  });

  Then('I click on the radio {string}', function (string) {
    return client
        .click(string)
  });

  Then('I click on the checkbox {string}', function (string) {
    return client
        .clickCheckboxIfUnselected(string)
  });

  Then('I upload the file {string} to field {string}', function (filepath, uploadfield) {
    return client.setValue(uploadfield, __dirname + '/' + filepath);
  });

  Then('the element {string} contains the text {string}', function (elName, elText) {
    return client.expect.element(elName).text.to.contain(elText)
  });

  Then('the element {string} does not contain the text {string}', function (string, string2) {
    return client.expect.element(string).text.to.not.contain(string2)
  });

  When('the element {string} is visible', function (string) {
    return client.waitForElementVisible(string, 10000);
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
    return client.waitForElementVisible(elName, 1000);
  });

  When('I select the option with the value {string} for element {string}', function (somevalue, myselectbox) {
    return client.click(myselectbox + ' option[value="'+ somevalue +'"]');
  });

  Given('I add {string} to the inputfield {string}', function (string, string2) {
    return client
        .expect.element(string2).to.be.present.then(function (){
          return client
            .setValue(string2, '')
            .setValue(string2, string);
      })
  });

  Given('I am logged in as {string}', function (string) {
    return shared
      .loggedInAs(string)
  });

  Then('I clear the inputfield {string}', function (elem) {
    return client
      .setValue(elem, '')
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


  Then('the element {string} is not empty', function (string) {
    return client
      .waitForElementVisible(string, 1000);
  });

  Then('the element {string} is empty', function (string) {
    return client
      .waitForElementVisible(string, 1000);
  });

  When('I click on authority selection if available', function () {
    return shared
    .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//div[text()="City Enforcement Squad"]')
  });

  When('I click on new organisation option if available', function () {
    return shared
      .chooseNewOrganisationOptionIfPresent('neworg','#edit-par-data-organisation-id-new')
  });

  When('I click new person if suggestions displayed', function () {
    return shared
      .chooseNewPersonIfOptionPresent('newperson','#edit-par-data-person-id-new') 
   });

   When('the {string} email confirmations for {string} are processed', function (emailSubject, user) {
    return shared
        .checkEmails(emailSubject, user)
  });

  When('only messages of type {string} are displayed', function (messageType) {
    return shared.checkMessageTypeDisplay(messageType);
  });

  When('I run tota11y against the current page', function () {
    return shared
      .runTota11yAgainstCurrentPage()
  });
  
  When('I go to partnership detail page for my partnership {string} with status {string}', function (orgname, status) {
    return shared
    .goToPartnershipDetailPage(orgname,status)
   }); 
   