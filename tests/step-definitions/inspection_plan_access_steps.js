const {client} = require('nightwatch-cucumber')
const {Given, Then, When} = require('cucumber')
const shared = client.page.shared()


Then('I should see inspection plans list page', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Inspection Plans')
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

When('I click on edit against an inspection plan', function () {
    return shared
        .clickLinkByPureText('Edit')
        .assert.containsText('h1.heading-xlarge', 'Edit inspection plan details')

});

Then('I enter new inspection plan title', function () {
    return client
        .clearValue('#edit-title')
        .setValue('#edit-title', 'Auto-test-NewInspectionPlan-retest')

});

Then('I enter new summary for an inspection plan', function () {
    return client
        .clearValue('#edit-summary')
        .setValue('#edit-summary', 'Auto-inspection-plan-Summary-retest')

});

Then('I see the inspection plan has updated successfully', function () {
    return shared
        .clickLinkByPureText('Auto-test-NewInspectionPlan-retest')
        .assert.containsText('h1.heading-xlarge', 'Auto-test-NewInspectionPlan-retest')
        .assert.containsText('#content', 'Auto-inspection-plan-Summary-retest')
});




