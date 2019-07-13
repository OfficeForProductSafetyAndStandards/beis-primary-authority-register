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
        clickUploadAdvice: function(){
        return client
        .click('a.flow-link')
        },
        clickLinkByPureText: function(linkText){
            return this.click('link text', linkText);
        },
        clickLinkByPartialText: function(partialLinkText){
            client.useXpath()
                .click("//a[contains(.,'" + partialLinkText + "')]")
                .useCss();
            return this;
        },
        putTextFromSelectorToAnotherSelector: function(selector1, input1){
            var text;
            this.getValue(selector1, function (result) {
                text = result.value;
                browser.setValue(input1, text);
          });
        },

     generaterandomString: function(length) {
     var text = "";
     var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

     for (var i = 0; i <length; i++)
     text += possible.charAt(Math.floor(Math.random() * possible.length));

     return text;
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
                .clickLinkByPureText('Sign in')
                .setValue('#edit-name', string)
                .setValue('#edit-pass', 'TestPassword')
                .click('#edit-submit')
                .waitForElementVisible('#footer', 15000)
                .assert.containsText('body', 'Sign out')
        },
        goToPartnershipDetailPage: function(search, name, status){
        return this
            .clickLinkByPureText('Dashboard')
            .clickLinkByPartialText('See your partnerships')
            .setValue('#edit-keywords', search)
            .click('#edit-partnership-status-1 option[value="'+status+'"]')
            .click('#edit-submit-par-user-partnerships')
            .clickLinkByPureText(name)
        },
        createNewPartnership: function(){
             return this
              .assert.containsText('.error-summary', 'Please confirm that all conditions for a new partnership have been met')
              .click('#edit-edit-confirm')
              .click('#edit-next')
              .click('#edit-next')
              .waitForElementVisible('.error-summary', 1000)
              .assert.containsText('.error-summary', 'Please confirm whether the organisation has been notified that any existing local authorities will continue to regulate it')
              .click('#edit-business-notified-2')
              .click('#edit-next')
              .assert.containsText('#par-partnership-about','Use this section to give a brief overview of the partnership')
              .setValue('#edit-about-partnership', 'About the partnership detail')
              .click('#edit-next')
        },
        searchSelectOrganisation: function(organisation){
            return this

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
        fillinContactDetails: function(){
          return client
        .useXpath()
        .click("//a[contains(.,'Add a person')]")
        // browser.assert.title('Create a new Person | Add contact details | Primary Authority Register')
        .useCss()
        .clearValue('#edit-salutation')
        .setValue('#edit-salutation','Mr')
        .clearValue('#edit-first-name')
        .setValue('#edit-first-name','Auto-Jack')
        .clearValue('#edit-last-name')
        .setValue('#edit-last-name','Auto-Ferndale')
        .clearValue('#edit-work-phone')
        .setValue('#edit-work-phone','0798888688')
        .clearValue('#edit-email')
        .setValue('#edit-email','AutoTest-' + this.generaterandomString(5) + '@test.com')
        .pause(2000)
        .click('#edit-next')
        .pause(3000)
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
