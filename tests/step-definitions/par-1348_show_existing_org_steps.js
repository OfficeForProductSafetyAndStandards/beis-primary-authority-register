const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared()


When('I click on {string}', function (string) {
	return shared
   .clickLinkByPureText('Apply for a new partnership')
   .chooseAuthorityIfOptionPresent('input[name="par_data_authority_id"]', '//label[text()="City Enforcement Squad"]');
    })

When('I select direct partnership', function () {
	return shared
	.click('#edit-application-type-direct')
    .click('#edit-next')
         });


When('I select coordinated partnership', function () {
	return shared
	.click('#edit-application-type-coordinated')
    .click('#edit-next')
         });

 When('I fill in all required fields', function () {
 	return client 
 	 	 .click('#edit-confirm')
 	 	 .click('#edit-next')
 	 	 .isVisible('.error-summary', results => {
      	 if (results.value) { 
      	 	return client
      	 .click('#edit-confirm')
 	 	 .click('#edit-next')
       	 }
 	 	})
  		 .assert.containsText('#par-partnership-about','Use this section to give a brief overview of the partnership')
  		 .clearValue('#edit-about-partnership')
  		 .setValue('#edit-about-partnership', 'About the partnership detail')
  		 .click('#edit-next')
  		 .assert.containsText('#par-partnership-application-organisation','Provide the business or organisation name')
  		 .pause(7000)
         });

 When('I enter existing organistaion {string}', function (string) {
       return client 
       .clearValue('#edit-name')
       .setValue('#edit-name',string)
       .click('#edit-next');
         });

  Then('I should be able to see Asda', function () {
  	return client
  	 browser.expect.element('#main').text.to.equal('Asda Stores Ltd');
  	//.assert.containsText('#organisation_name','Asda Stores Ltd')
          
         });

 	Then('I should be able to see {string}', function (OrgName) {
 		return client
 	 browser.expect.element('#main').text.to.equal(OrgName);
           
         });

   When('I select existing organisation {string}', function (string) {
   	return client
   	.useXpath()
   	.click("//div[@class='organisation_name']")
   	.click("//input[@id='edit-next']")
   	.useCss()
           
         });

   Then('I should be able to complete partnership', function () {
   	     return client
   	     .waitForElementPresent('h1.heading-xlarge',6000)
   	     .assert.containsText('h1.heading-xlarge','Check partnership information')
          
         });

