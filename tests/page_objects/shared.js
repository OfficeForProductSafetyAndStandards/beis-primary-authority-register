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
        chooseAuthorityIfOptionPresent: function(elem, toclick){ 
            return this.api.element('css selector', 'input[name="par_data_authority_id"]' , function (result) {
                if (result.value.ELEMENT) {
                    return this                   
                    .click(toclick)    
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
                    .click(toclick)  
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
        }
      }]
}