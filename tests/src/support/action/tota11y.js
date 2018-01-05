/**
 * Perform an click action on the given element
 * @param  {String}   action  The action to perform (click or doubleClick)
 * @param  {String}   type    Type of the element (link or selector)
 * @param  {String}   element Element selector
 */
module.exports = (done) => {
    /**
     * The method to call on the browser object
     * @type {String}
     */
    const isVisible = browser.isVisible('.tota11y-info-error-count');
    browser[click](".tota11y-toolbar-toggle");
    browser[click](".tota11y-plugin-title*=Headings");
    expect(isVisible).to.not.equal(true);
    browser[click](".tota11y-plugin-title*=Contrast");
    expect(isVisible).to.not.equal(true);
    browser[click](".tota11y-plugin-title*=Link text");
    expect(isVisible).to.not.equal(true);
    browser[click](".tota11y-plugin-title*=Labels");
    expect(isVisible).to.not.equal(true);
    browser[click](".tota11y-plugin-title*=Image alt-text");
    expect(isVisible).to.not.equal(true);
    browser[click](".tota11y-toolbar-toggle");
};
