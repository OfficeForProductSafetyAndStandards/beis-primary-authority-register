/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const urlMemberAdd = browser.getUrl() + '/members/add';
    console.log(urlMemberAdd);
    browser.url(urlMemberAdd);
};
