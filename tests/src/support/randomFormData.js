(function () {
    "use strict";
    var moment = require('moment');
    var _ = require('lodash');
    /** Helper for entering valid form data when running e2e tests */
    module.exports = {
        /** Generates some random text based on n no. of characters */
        text: function (n) {
            var text = '';
            var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ';
            for (var i = 0; i < n; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        },
        /** Generates a unique id based on the number of milliseconds from 1st January 1970
         * @returns 1477636793870
         */
        uniqueid: function now() {
            return new Date().getTime();
        },
        /** Return a  delimited string */
        delimitedString: function (delimiter, n) {
            var arr = [];
            for (var i = 0; i < n; i++) {
                arr.push(this.text(5));
            }
            return arr.join(delimiter + ' ');
        },
        /** Returns a random number between two values */
        numberBetween: function (min, max) {
            return Math.floor(Math.random() * (max - min) + min);
        },
        /** Returns a date in a valid format. A day offsets can be passed to adjust the date */
        date: function (dayOffset) {
            var now = moment();
            if (dayOffset != null && _.isFinite(dayOffset)) {
                now = now.add(dayOffset, 'days');
            }
            return now.format('DD/MM/YYYY');
        }
    };
})();
