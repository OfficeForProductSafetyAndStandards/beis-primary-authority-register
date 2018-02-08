/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    browser.setValue('#edit-pass-pass1', 'TestPassword');
    browser.setValue('#edit-pass-pass2', 'TestPassword');
    browser.click('#edit-next');
};
