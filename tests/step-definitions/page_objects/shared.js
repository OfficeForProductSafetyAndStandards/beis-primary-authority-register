const { client } = require('nightwatch-cucumber')

module.exports = {
    url: 'http://localhost:8111',
    elements: {
        googleSearchField: 'input[name="q"]',
        selector: 'input[name="par_data_authority_id"]',
        newperson: 'input[name="par_data_person_id"]',
        neworg: 'input[name="par_data_organisation_id"]',
        Logout: '.button'
    },
    commands: [{
        clickLinkByText: function(linkText){      
           linkText = linkText.replace(/\s/g, '');  
           console.log(linkText);  
           return this
            .waitForElementVisible('@'+linkText, 1000)
            .click('@'+linkText);
        },
        clickLinkByPureText: function(linkText){      
            return this.click('link text', linkText);
        },
        putTextFromSelectorToAnotherSelector: function(selector1, input1){ 
            var text;
            this.getValue(selector1, function (result) {
                text = result.value;
                browser.setValue(input1, text);
          });
        },  
        chooseAuthorityIfOptionPresent: function(elem, toClick){ 
            return this.api.element('css selector', elem , function (result) {
                if (result.value.ELEMENT) {
                    return this  
                    .useXpath()
                    .click(toClick)   
                    .useCss()                 
                    .click('#edit-next');
                }
                else{ 
                    return this
                    }
              })  
        },
        chooseNewPersonIfOptionPresent: function(elem, toclick){ 
            return this.api.element('css selector', 'input[name="par_data_person_id"]', function(result){
                if (result.value.ELEMENT) {
                    return this
                    .click('#edit-par-data-person-id-new')  
                    .click('#edit-save');
                  } else
                  {
                    return this
                  }
            })   
        },
        chooseNewOrganisationOptionIfPresent: function(elem, toclick){ 
            return this.api.element('css selector', 'input[name="par_data_organisation_id"]', function(result){
                if (result.value.ELEMENT) {
                    return this
                    .click(toclick)  
                    .click('#edit-next');
                  } else
                  {
                    return this
                  }
            })   
        },
        clickShowMembersListIfPresent: function(){ 
            return this.api.element('css selector', 'edit-members-link p:nth-child(2)', function(result){
                if (result.value.ELEMENT) {
                    return this
                    .clickLinkByPureText('Show members list')  
                  } else
                  {
                    return this
                  }
            })   
        },
        checkMessageTypeDisplay: function(messageType){ 
            let element = null;
            this.api.elements('css selector', 'td.views-field.views-field-par-status', function (elements) {
              let success = false;
              for (let i = 0; (i < elements.value.length) && (success === false); i++) {
                this.elementIdText(elements.value[i].ELEMENT, function (result) {
                    this.assert.equal(result.value, messageType)
                  if (result.value == messageType) {
                    console.log(result.value);
                  }
                });
              }
            });
        },
        checkEmails: function(string, string2){ 
            var emailSubject = ''
            switch (string) 
            {
                case 'enforcement creation': emailSubject = 'Primary Authority: Notification of Proposed Enforcement'; break;
                case 'partnership approval': emailSubject = 'Primary Authority: Partnerships Nominated'; break;
                case 'partnership revocation': emailSubject = 'Primary Authority: Notification of Partnership Revocation'; break;
                case 'partnership completed': emailSubject = 'Primary Authority: Partnership Application Completed'; break;
                case 'partnership invitation': emailSubject = 'Primary Authority: Invitation to join the Primary Authority Register'; break;
            }
            console.log(emailSubject)
            return this
            .click('#block-par-theme-account-menu > ul > li:nth-child(3) > a')
            .click('#block-par-theme-account-menu > ul > li > a')
            .setValue('#edit-name','par_admin@example.com')
            .setValue('#edit-pass','TestPassword')
            .click('#edit-submit')
            .clickLinkByPureText('Reports')
            .clickLinkByPureText('Maillog')
            .setValue('#edit-header-to',string2)
            .click('#edit-submit-maillog-overview')
            .clickLinkByPureText(emailSubject)
            // .click('#block-seven-content > div > div > div.view-content > table > tbody > tr:nth-child(1) > td.views-field.views-field-subject > a')
            .assert.containsText('h1.heading-xlarge', emailSubject)
            .assert.containsText('#block-par-theme-content',string2)
        }
    }]
}