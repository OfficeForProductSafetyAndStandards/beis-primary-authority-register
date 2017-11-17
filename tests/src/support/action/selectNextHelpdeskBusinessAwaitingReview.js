/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    browser.setViewportSize({
        width: 1024,
        height: 768,
    });
    browser.clearElement('#edit-keywords');
    browser.selectByValue('#edit-partnership-status', '1');
    browser.click('#edit-submit-rd-helpdesk-dashboard');
    browser.click('td.views-field.views-field-organisation-name a');
};
