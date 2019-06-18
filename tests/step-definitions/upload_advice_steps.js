const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared()



Given('I navigate to an active partnership {string}', function (string){
            return client
           .url(client.launch_url+'partnership/'+string+'/authority-details')
         });

Then('I should see advice page', function () {
            return shared
            .assert.containsText('h1.heading-xlarge','Advice')
          });

Then('I should not see the link {string}', function (string) {
            return client
            .expect.element('a.flow-link').to.be.not.present

         });



Then('I see the link {string}', function (string) {
           return client
.assert.containsText('a.flow-link','Upload advice')
           });

Given('I open an active partnership {string}', function (string) {
          return shared
          .goToPartnershipDetailPage(string,'active')
         });

Given('I open advice page', function () {
           return shared
            .clickLinkByPureText('See all Advice')
            .clickUploadAdvice()
         });

Then('I should be able to confirm the guidelines', function () {
      return client
      .click('#edit-declaration')
     .click('#edit-save')
              });


 Then('I able to upload advice document', function () {
       return client
       .setValue('#edit-files-upload','files/test.png')
       .click('#edit-upload')
         });


Then('enter advice title', function () {
 return client
  .clearValue('#edit-advice-title')
  .setValue('#edit-advice-title','Auto-test-NewAdvice')

         });
Then('I enter summary of advice', function () {
           return client
           .clearValue('#edit-notes')
           .setValue('#edit-notes','Auto-advice-Summary')

         });

   Then('I select advice type {string}', function (string) {
       return client
        .click('#edit-advice-type-'+string)

         });

  Then('I select {string} regulatory function', function (string) {
                   return client
                   .useXpath()
                   .click('//label[contains(text(),\"'+string+'"\)]')
                   .useCss()
                  });


 Then('I select type of advice', function () {
           return client
           .click('#edit-advice-type-business-advice')
         });

 Then('I select regulatory function', function () {
           return client
           .clickCheckboxIfUnselected('#edit-regulatory-functions-13')
         });


         Then('I see advice uploaded successfully', function () {
           return shared
           .clickLinkByPureText('Auto-test-NewAdvice')

           .assert.containsText('.filename','test')
         });


         When('I click on edit against an advice', function () {
           return shared
           .clickLinkByPureText('Edit')
           .assert.containsText('h1.heading-xlarge','Edit document type')

         });


         When('I click on archive against an advice', function () {
                    return shared
                    .clickLinkByPureText('Archive')
                    .assert.containsText('h1.heading-xlarge','Are you sure you want to archive this advice')

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
          .assert.containsText('.file','Advice Issued - 01 - Attic Descriptions v2_1')
          .click('#edit-save')

         });

         When('I enter reason {string}', function (string) {
                    return client
                    .setValue('#edit-archive-reason',string)
                  });



                  Then('I should archive successfully', function () {
                    return client
                    .assert.containsText('h1.heading-xlarge','Advice')
                   //.assert.containsText('.views-field views-field-par-status','Archived')
                  });

