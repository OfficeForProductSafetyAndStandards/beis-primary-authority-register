module.exports = (keyword, done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
        // browser.setViewportSize({
        //     width: 1024,
        //     height: 768,
        // });

        browser.saveScreenshot('./tests/errorShots/travis.png');
};