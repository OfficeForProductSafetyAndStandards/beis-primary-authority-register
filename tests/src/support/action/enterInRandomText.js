import checkIfElementExists from '../lib/checkIfElementExists';

/**
 * Set the value of the given input field to a new value or add a value to the
 * current element value
 * @param  {String}   numChars  The method to use (add or set)
 * @param  {String}   fieldName  The method to use (add or set)
 */
module.exports = (numChars, fieldName) =>
{
    let text = '';
    let n = numChars;
    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ';
    for (var i = 0; i < n; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    browser.addValue(fieldName, text + 'last text in a long string');
}
