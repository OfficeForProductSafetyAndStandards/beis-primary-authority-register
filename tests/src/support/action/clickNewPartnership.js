/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 * @param  {String}   keyword       The search keyword
 */

module.exports = () =>
{
    let isVisible = browser.isVisible('#edit-par-data-organisation-id-new');
    if (isVisible = true) {
        browser.click('#edit-next');
    } else {
    }
};
