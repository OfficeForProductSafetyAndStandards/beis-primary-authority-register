/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 * @element  {Function} done          Function to execute when finished
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
    const q = require('q');
    const checks = [];
    browser.url('/dv/rd-dashboard?keywords=sale&partnership_status=All');
    browser.elements('tbody tr').getText().then(function (res){
        res.value.forEach(function (text) {
            expect(text).to.contain('sale');
        });
        return q.all(checks);
    });
};
