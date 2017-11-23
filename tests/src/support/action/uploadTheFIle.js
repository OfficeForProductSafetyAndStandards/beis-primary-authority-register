/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

module.exports = (filename) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    const toUpload = 'src/features/files/test1.png';
    browser.chooseFile('#edit-files-upload', filename);
};
