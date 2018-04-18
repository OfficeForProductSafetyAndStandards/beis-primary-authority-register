const { client } = require('nightwatch-cucumber')
const { Given, Then, When } = require('cucumber')
const shared = client.page.shared();


When('the {string} confirmations for {string} are processed', function (string, string2) {
    return shared
        .checkEmails(string,string2)
});
