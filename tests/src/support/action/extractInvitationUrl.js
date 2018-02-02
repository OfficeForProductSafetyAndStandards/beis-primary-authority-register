/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const urlInvite = browser.getText('div.form-group:nth-child(5) > pre:nth-child(2)');
    const urlToUse = urlInvite.match(/\bhttps?:\/\/\S+/gi);
    browser.url('/user/logout');
    console.log(urlToUse[0]);
    browser.url(urlToUse[0]);
};
