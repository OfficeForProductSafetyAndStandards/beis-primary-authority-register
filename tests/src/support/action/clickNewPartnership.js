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

    const isVisible = browser.isVisible('#edit-par-data-organisation-id-new');
    if (isVisible) {
        browser.click('#edit-par-data-organisation-id-new');
    } else {
    }
    done();
};
