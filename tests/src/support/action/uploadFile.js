/**
 * Set the value of the given input field to a new value or add a value to the
 * current element value
 * @param  {Function} done    Function to execute when finished
 */
module.exports = (done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const selector = '#edit-files-upload';
    const toUpload = '/Users/jaffamonkey/projects/beis-par-beta/tests/src/files/eicar.pdf';
    browser.chooseFile(selector, toUpload);
    done();
};

