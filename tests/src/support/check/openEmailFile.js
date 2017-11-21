/**
 * Check if the given elements text is the same as the given text
 * @param  {Function} done          Function to execute when finished
 */

const fs = require('fs-extra');

module.exports = () => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */
    fs.readFile('/emailfile.txt', function (err, data) {
        if (err) throw err;
        console.log(data);
    });
};




