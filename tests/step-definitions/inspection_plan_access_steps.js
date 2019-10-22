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

Given('I open inspection plan add page', function () {
    return shared
        .clickLinkByPureText('See all Inspection Plans')
        .clickLinkByPureText('Upload inspection plan')
});

Then('I enter inspection plan title', function () {
    return client
        .clearValue('#edit-title')
        .setValue('#edit-title', 'Auto-test-NewInspectionPlan')

});

Then('I enter summary of inspection plan', function () {
    return client
        .clearValue('#edit-summary')
        .setValue('#edit-summary', 'Auto-advice-NewInspectionPlan')

});

Then('I see inspection plan uploaded successfully', function () {
    return shared
        .clickLinkByPureText('Auto-test-NewInspectionPlan')
        .assert.containsText('.filename', 'test')
});

