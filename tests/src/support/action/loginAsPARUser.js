/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (username) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    browser.setViewportSize({
        width: 1200,
        height: 2000
    });
    browser.url('/user/login');
    browser.setValue('#edit-name', username);
    browser.setValue('#edit-pass', 'TestPassword');
    browser.click('#edit-submit');
};
