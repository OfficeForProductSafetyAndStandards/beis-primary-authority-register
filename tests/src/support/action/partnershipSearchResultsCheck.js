/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 * @param  {String}   keyword       The search keyword
 */

module.exports = (keyword, done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
        // browser.setViewportSize({
        //     width: 1024,
        //     height: 768,
        // });
    browser.setValue('#edit-keywords', keyword);
    browser.click('#edit-submit-partnership-search');
    const links = $$('td.views-field.views-field-authority-name');
    links.forEach(function (link) {
        let elem = link.getText();
        expect(elem).to.contain(keyword);
    });
    done();
};
