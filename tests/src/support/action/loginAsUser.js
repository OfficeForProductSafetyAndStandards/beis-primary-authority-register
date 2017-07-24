/**
 * Check if the given elements text is the same as the given text
 * @param  {String}   type          Type of element (inputfield or element)
 * @param  {String}   element       Element selector
 * @param  {String}   falseCase     Whether to check if the content equals the
 *                                  given text or not
 * @param  {String}   expectedText  The text to validate against
 * @param  {Function} done          Function to execute when finished
 */
module.exports = (method, username, password, done) => {
    /**
     * The command to execute on the browser object
     * @type {String}
     */
   const command = (method === 'add') ? 'addValue' : 'setValue';
    /**
     * Function to execute when finished
     * @type {Function}
     */

    browser[command]('#edit-name', username);
    browser[command]('edit-pass', password);
    browser.click('#edit-submit');

    done();
};
