/**
 * Perform an click action on the given element
 * @param  {String}   action  The action to perform (click or doubleClick)
 * @param  {String}   type    Type of the element (link or selector)
 * @param  {String}   element Element selector
 * @param  {Function} done    Function to execute when finished
 */
module.exports = () =>
{
    /**
     * Element to perform the action on
     * @type {String}
     */
    /**
     * The method to call on the browser object
     * @type {String}
     */
    //const nrOfElements = browser.elements('#par-authority-selection').value;
    //if (nrOfElements != 0) {
        browser.element('.form-label*=City Enforcement Squad').click();
        //browser.element('#edit-next').click();
    //}
    //else {
    //    done();
    //}
};
