/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    String.prototype.splice = function(idx, rem, str) {
        return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
    };
    browser.setViewportSize({
        width: 1024,
        height: 768,
    });
    var primarycontact = process.env.TEST_DISPLAYNAME;
    var primaryContact = primarycontact.splice(4, 0, " ");
    console.log(primaryContact);
    browser.element('a*=edit ' + primaryContact).click();
    done();
};
