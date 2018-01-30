/**
 * Perform an click action on the given element
 * @param  {String}   action  The action to perform (click or doubleClick)
 * @param  {String}   type    Type of the element (link or selector)
 * @param  {String}   element Element selector
 */
module.exports = () =>
{
    browser.click('.tota11y-toolbar-toggle');
    var list = ['Headings', 'Contrast', 'Link text', 'Labels', 'Image alt-text'];
    for (var i = 0; i < list.length; i++) {
        browser.click('.tota11y-plugin-title*=' + list[i]);
        if (browser.isVisible('.tota11y-info-errors') === true) {
            const errors = browser.getText('.tota11y-info-errors');
            var url = browser.getUrl();
            console.log(url, errors);
            //expect(browser.isVisible('body')).to.equal(true, errors);
        }
    }
    browser.click('.tota11y-toolbar-toggle');
};
