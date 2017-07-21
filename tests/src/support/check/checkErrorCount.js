/**
 * Check if the given elements text is the same as the given text
 * @param  {String}   type          Type of element (inputfield or element)
 * @param  {String}   element       Element selector
 * @param  {String}   falseCase     Whether to check if the content equals the
 *                                  given text or not
 * @param  {String}   expectedText  The text to validate against
 * @param  {Function} done          Function to execute when finished
 */
module.exports = (count, done) => {
    /**
     * The command to execute on the browser object
     * @type {String}
     */
    let count2 = parseInt(count);
    const nrOfElements = browser.elements('.error-message').value;
    expect(nrOfElements.length).to.be.equal(count2);
    // done();
};
