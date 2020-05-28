const {client} = require('nightwatch-cucumber')
const {Given, Then, When} = require('cucumber')
const shared = client.page.shared()


Then('I should see inspection plans list page', function () {
    return shared
        .assert.containsText('h1.heading-xlarge', 'Inspection Plans')
});

Then('I should see inspection plans search list page', function () {
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

Then('I enter the inspection plan title {string}', function (title) {
    return client
        .clearValue('#edit-title')
        .setValue('#edit-title', title)

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

When('I edit the inspection plan {string}', function (title) {
    return shared
        .setValue('#edit-keywords', title)
        .click('#edit-submit-inspection-plan-lists')
        .clickLinkByPureText('Edit inspection plan')
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

When('I revoke the inspection plan {string}', function (title) {
    return shared
        .setValue('#edit-keywords', title)
        .click('#edit-submit-inspection-plan-lists')
        .clickLinkByPureText('Revoke inspection plan')
        .assert.containsText('h1.heading-xlarge', 'Are you sure you want to revoke this inspection plan?')
});

When('I remove the inspection plan {string} with the reason {string}', function (inspection_plan, reason) {
    return shared
        .setValue('#edit-keywords', inspection_plan)
        .click('#edit-submit-inspection-plan-lists')
        .clickLinkByPureText('Remove inspection plan')
        .assert.containsText('h1.heading-xlarge', 'Are you sure you want to remove this inspection plan?')
        .click('#edit-next')
        .waitForElementVisible('.error-summary', 1000)
        .assert.containsText('.error-summary', 'Please enter the reason you are removing this inspection plan.')
        .setValue('#edit-remove-reason', reason)
        .click('#edit-next')
});

When('I enter the revoke reason {string}', function (string) {
    return client
        .setValue('#edit-revocation-reason', string)
});

When('I enter the deletion reason {string}', function (string) {
    return client
        .setValue('#edit-deletion-reason', string)
});

Then('I should revoke successfully', function () {
    return shared
        .clickLinkByPureText('Auto-test-NewInspectionPlan-retest')
        .assert.containsText('h1.heading-xlarge', 'Auto-test-NewInspectionPlan-retest')
        .assert.containsText('#block-par-theme-content','This inspection plan has been revoked and is for reference only, please do not use it for an inspection.')
});

When('I go to manage the partnership {string} click on {string} and status {string}', function (search, name, status) {
    return shared
        .goToManagePartnershipPage(search,name,status)
});



