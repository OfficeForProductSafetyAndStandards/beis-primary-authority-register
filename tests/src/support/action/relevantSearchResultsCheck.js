/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    // browser.setViewportSize({
    //     width: 1024,
    //     height: 768,
    // });
    browser.url('/dv/partnership-dashboard?partnership_status=1');
    const text = browser.getText('.table-scroll-wrapper');
    expect(text).to.contain('Awaiting Review');
    done();
};
