/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 * @param  {String}   keyword       The search keyword
 */

module.exports = (done) =>
{
    process.env['TEST_USER'] = browser.getValue('#edit-mail');
    console.log(browser.getValue('#edit-mail'));
    done();
};
