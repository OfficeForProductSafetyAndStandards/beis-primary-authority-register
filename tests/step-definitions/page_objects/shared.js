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
        clickLinkByXpath: function(xpathValue){      
           return client
           .useXpath()
           .click(xpathValue)   
           .useCss()                 
           .click('#edit-next')
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
        chooseMemberIfOptionPresent: function(){ 
            return this.api.element('css selector', 'input[name="par_data_organisation_id"]', function(result){
                if (result.value.ELEMENT) {
                    return this
                    .click('.form-radio')  
                    .click('#edit-next');
                  } else
                  {
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
        },
        loggedInAs:function(string){ 
            return this
                .clickLinkByPureText('Log in')
                .setValue('#edit-name', string)
                .setValue('#edit-pass', 'TestPassword')
                .click('#edit-submit')
                .waitForElementVisible('#footer', 15000)
                .assert.containsText('body', 'Log out')
        },
        goToPartnershipDetailPage: function(orgName, status){ 
        return this
            .clickLinkByPureText('Dashboard')
            .clickLinkByPureText('See your partnerships')
            .setValue('#edit-keywords', orgName)
            .click('#edit-partnership-status-1 option[value="'+status+'"]')
            .click('#edit-submit-par-user-partnerships')
            .clickLinkByPureText(orgName)
        },
        runTota11yAgainstCurrentPage: function(){ 
            return this.click('.tota11y-toolbar-toggle')
            var list = ['Headings', 'Contrast', 'Link text', 'Labels', 'Image alt-text'];
            for (var i = 0; i < list.length; i++) {
                this.click('.tota11y-plugin-title*=' + list[i]);
                if (client.isVisible('.tota11y-info-errors') === true) {
                    const errors = browser.getText('.tota11y-info-errors');
                    var url = browser.getUrl();
                    console.log(url, errors);
                    //expect(browser.isVisible('body')).to.equal(true, errors);
                }
            }
            return this.click('.tota11y-toolbar-toggle');
        },
        clickCheckboxIfUnselected: function(string) {
            return this.element('id', string, (response) => {
                    this.elementIdSelected(response.value.ELEMENT, (result) => {
                      if(result.value == false) {
                        this.click(string)
                      };
                    });
            });
        }
    }]
}
