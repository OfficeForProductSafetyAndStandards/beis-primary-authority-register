const VisualReview = require('visualreview-protractor');
var vr = new VisualReview({
    hostname: '127.0.0.1',
    port: 7000,
});

exports.config = {

    specs: [
        'spec.js'
    ],

    capabilities: {
        browserName: 'chrome'
    },

    framework: 'jasmine2',

    beforeLaunch: function () {
        // Creates a new run under project name 'myProject', suite 'mySuite'.
        return vr.initRun('myProject', 'mySuite');
    },

    afterLaunch: function (exitCode) {
        // finalizes the run, cleans up temporary files
        return vr.cleanup(exitCode);
    },

    // expose VisualReview protractor api in tests
    params: {
        visualreview: vr
    }

};

