const {client} = require('nightwatch-cucumber')
const {Given, Then, When} = require('cucumber')
const shared = client.page.shared()


Then('I should see inspection plans list page', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Inspection plans')
});

Then('I see the inspection plan link {string}', function (string) {
    return client
        .assert.containsText('a.flow-link', string)
});
