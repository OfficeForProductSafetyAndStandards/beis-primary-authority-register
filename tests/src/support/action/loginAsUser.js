/**
 * Check if the given elements text is the same as the given text
 * @param  {String}   username      The text to validate against
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (method, value, done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const command = (method === 'add') ? 'addValue' : 'setValue';
    browser[command]('#edit-name', value);
    browser[command]('#edit-pass', 'TestPassword');
    browser.click('#edit-submit');
    done();
};
