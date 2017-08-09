import checkIfElementExists from '../lib/checkIfElementExists';

/**
 * Set the value of the given input field to a new value or add a value to the
 * current element value
 * @param  {String}   method  The method to use (add or set)
 * @param  {Array}   table   The value to set the element to
 * @param  {Function} done    Function to execute when finished
 */
module.exports = (method, table, done) => {
    /**
     * The command to perform on the browser object (addValue or setValue)
     * @type {String}
     */

    const data = table.hashes();

    const command = (method === 'add') ? 'addValue' : 'setValue';

    for (var i = 0; i < data.length; i++) {
        var inputData = data[i].field;
        var inputValue = data[i].content;
        checkIfElementExists(element, false, 1);
        if (!value) {
            browser[command](element, '');
        } else {
            browser.clearElement(element);
            var p = browser[command](inputData, inputValue);
        }
        if (i === data.length - 1) {
            p.then(done);
        }
    }
}
