'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _seleniumStandalone = require('selenium-standalone');

var _seleniumStandalone2 = _interopRequireDefault(_seleniumStandalone);

var _fsExtra = require('fs-extra');

var _fsExtra2 = _interopRequireDefault(_fsExtra);

var _getFilePath = require('./utils/getFilePath');

var _getFilePath2 = _interopRequireDefault(_getFilePath);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var DEFAULT_LOG_FILENAME = 'selenium-standalone.txt';

var SeleniumStandaloneLauncher = function () {
    function SeleniumStandaloneLauncher() {
        _classCallCheck(this, SeleniumStandaloneLauncher);

        this.seleniumLogs = null;
        this.seleniumArgs = {};
        this.seleniumInstallArgs = {};
        this.logToStdout = false;
    }

    _createClass(SeleniumStandaloneLauncher, [{
        key: 'onPrepare',
        value: function onPrepare(config) {
            var _this = this;

            this.seleniumArgs = config.seleniumArgs || {};
            this.seleniumInstallArgs = config.seleniumInstallArgs || {};
            this.seleniumLogs = config.seleniumLogs;
            this.logToStdout = config.logToStdout;

            return this._installSeleniumDependencies(this.seleniumInstallArgs).then(function () {
                return new Promise(function (resolve, reject) {
                    return _seleniumStandalone2.default.start(_this.seleniumArgs, function (err, process) {
                        if (err) {
                            return reject(err);
                        }

                        _this.process = process;
                        if (typeof _this.seleniumLogs === 'string') {
                            _this._redirectLogStream();
                        }

                        resolve();
                    });
                });
            });
        }
    }, {
        key: 'onComplete',
        value: function onComplete() {
            if (this.process) {
                this.process.kill();
            }
        }
    }, {
        key: '_installSeleniumDependencies',
        value: function _installSeleniumDependencies(seleniumInstallArgs) {
            return new Promise(function (resolve, reject) {
                return _seleniumStandalone2.default.install(seleniumInstallArgs, function (err) {
                    if (err) {
                        return reject(err);
                    }

                    resolve();
                });
            });
        }
    }, {
        key: '_redirectLogStream',
        value: function _redirectLogStream() {
            var logFile = (0, _getFilePath2.default)(this.seleniumLogs, DEFAULT_LOG_FILENAME);

            // ensure file & directory exists
            _fsExtra2.default.ensureFileSync(logFile);

            var logStream = _fsExtra2.default.createWriteStream(logFile, { flags: 'w' });
            this.process.stdout.pipe(logStream);
            this.process.stderr.pipe(logStream);
        }
    }]);

    return SeleniumStandaloneLauncher;
}();

exports.default = SeleniumStandaloneLauncher;
