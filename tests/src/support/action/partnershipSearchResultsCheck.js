/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 * @param  {String}   keyword       The search keyword
 */

module.exports = (done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
        // browser.setViewportSize({
        //     width: 1024,
        //     height: 768,
        // });

    if (element) {
        expect(isVisible).to.not
            .equal(true, `Expected element "${element}" not to be visible`);
    } else {
        expect(isVisible).to
            .equal(true, `Expected element "${element}" to be visible`);
    }

    done();
    browser.setValue('#edit-keywords', keyword);
    browser.click('#edit-submit-par-user-partnerships');
    const links = $$('td.views-field.views-field-authority-name');
    links.forEach(function (link) {
        let elem = link.getText();
        expect(elem).to.contain(keyword);
    });
    done();
};
