var assert = require('assert')

module.exports = function () {
    this.Given(/^I go on the website "([^"]*)"$/, (url) => {
        browser.url(url)
    })

    this.Then(/^I click on link "([^"]*)"$/, (selector) => {
        browser.click(selector)
    })

    this.Then(/^should the title of the page be "([^"]*)"$/, (expectedTitle) => {
        assert.equal(browser.getTitle(), expectedTitle)
    })
}
