/**
 * Set the value of the given input field to a new value or add a value to the
 * current element value
 * @param  {String} elem    upload field
 * @param  {String} filename    input file path
 */
module.exports = (filename, elem) =>
{
    browser.chooseFile(elem, __dirname + '/' + filename);
}
;
