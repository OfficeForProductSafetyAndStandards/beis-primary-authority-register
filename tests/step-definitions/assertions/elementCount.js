// assert.elementCount('element', 1)
exports.assertion = function (selector, count) {
    var countEl = parseInt(count, 10);
    this.message = 'Testing if element <' + selector + '> has count: ' + countEl;
    this.expected = countEl;
    this.pass = function (val) {
      return val === this.expected;
    }
    this.value = function (res) {
      return res.value;
    }
    this.command = function (cb) {
      var self = this;
      return this.api.execute(function (selector) {
        return document.querySelectorAll(selector).length;
      }, [selector], function (res) {
        cb.call(self, res);
      });
    }
  }
