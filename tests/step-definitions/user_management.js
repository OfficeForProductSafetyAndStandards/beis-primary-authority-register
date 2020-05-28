const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();


 Given('I login as {string}"', function (string) {
          return shared
      .loggedInAs(string)
         });
Then('I should not see {string}', function (string) {
           return client
           browser.expect.element('#main').text.to.not.equal(string);
         });

 When('I click {string}', function (string) {
 	   return shared
      .clickLinkByPureText(string)
         });

  When('I fill in add new person form', function () {
        return shared
        .fillinContactDetails()
         });

  Given('I click on create account', function () {
           return client
           .click('#edit-account-new')
           .click('#edit-next')
         });

    Given('I select authority as {string}', function (string) {
          return client
          .useXpath()
          .click("//label[contains(text(),'"+ string+"')]")
          .useCss()
   		  .click('#edit-next')
         });

     Given('I select type of user {string}', function (string) {
     	  return client
     	   .click('#edit-role-par-'+string)
     	   .click('#edit-next')
         });

 Given('I click on continue invite user', function () {
           return client
           .click('#edit-next')

         });
  When('I click save', function () {
          return client
           .click('#edit-save')

         });
  When('I click continue', function () {
          return client
           .click('#edit-next')

         });

   Then('I should see confirmation message {string}', function (string) {
           return client
   	     .waitForElementPresent('h1.heading-xlarge',6000)
   	    browser.expect.element('#main').text.to.not.equal(string);
   	     //.assert.containsText('h1.heading-xlarge',"You're new person has been created")

         });


  Given('And I review the user details on review page',function(){
   return client
   .expect.element('#edit-email').text.to.contain('AutoTest')
   .expect.element('#edit-first-name').text.to.contain('Auto-Jack')

  });

  Then('I should see updated details{string} and {string}', function (string, string2) {
           return client.expect.element(string).text.to.contain(string2)
         });

  Then('I should be able to save updated changes', function () {
           return client
           .click('#edit-save')

         });

   Then('I should see updated details {string} and {string}', function (string, string2) {
           return client.expect.element(string).text.to.contain(string2)
         });


 Given('I review the user details on review page', function () {
 	    return client
 		.expect.element('#edit-email').text.to.contain('AutoTest')
        //.expect.element('#edit-first-name').text.to.contain('Auto-Jack')
         });
