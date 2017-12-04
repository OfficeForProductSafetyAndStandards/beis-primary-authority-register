import checkIfElementExists from '../lib/checkIfElementExists';

/**
 * Perform an click action on the given element
 * @param  {String}   action  The action to perform (check or uncheck)
 * @param  {String}   element Element selector
 */
module.exports = (action, element) => {

    checkIfElementExists(element, false, 1);

    if (action === 'check') {
        if (!browser.isSelected(element)) {
            browser.click(element);
        }
    }

    if (action === 'uncheck') {
        if (browser.isSelected(element)) {
            browser.click(element);
        }
    }

};
