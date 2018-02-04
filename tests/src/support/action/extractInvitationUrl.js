/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const urlInvite = browser.getText('#block-par-theme-content');
    const urlToUse = urlInvite
        .match(/\bhttps?:\/\/\S+\/invite\/accept\/[a-zA-Z0-9]+/g)[0];
    browser.url('/user/logout');
    // Prevent needing to follow redirect by changing URL.
    const url = urlToUse.replace('invite', 'par-invite');
    console.log('invite URL extracted from email', urlToUse);
    console.log('invite URL to be requested', url);
    browser.url(url);
};
