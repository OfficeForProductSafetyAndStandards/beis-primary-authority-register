/**
 * Open the given URL
 * @param  {Function} done Function to execute when finished
 */
module.exports = (done) => {
    /**
     * The URL to navigate to
     * @type {String}
     */
    const supertest = require('supertest');
    const api = supertest('url');

    api.get('/')
    .set('Accept', 'application/json')
    .expect(200)
    .end(done);
    // done();
};
