'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.__RewireAPI__ = exports.__ResetDependency__ = exports.__set__ = exports.__Rewire__ = exports.__GetDependency__ = exports.__get__ = undefined;

var _isExtensible = require('babel-runtime/core-js/object/is-extensible');

var _isExtensible2 = _interopRequireDefault(_isExtensible);

var _keys = require('babel-runtime/core-js/object/keys');

var _keys2 = _interopRequireDefault(_keys);

var _typeof2 = require('babel-runtime/helpers/typeof');

var _typeof3 = _interopRequireDefault(_typeof2);

var _defineProperty = require('babel-runtime/core-js/object/define-property');

var _defineProperty2 = _interopRequireDefault(_defineProperty);

var _create = require('babel-runtime/core-js/object/create');

var _create2 = _interopRequireDefault(_create);

var _getIterator2 = require('babel-runtime/core-js/get-iterator');

var _getIterator3 = _interopRequireDefault(_getIterator2);

var _classCallCheck2 = require('babel-runtime/helpers/classCallCheck');

var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

var _createClass2 = require('babel-runtime/helpers/createClass');

var _createClass3 = _interopRequireDefault(_createClass2);

var _wdioSync = require('wdio-sync');

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var CUCUMBER_EVENTS = ['handleBeforeFeatureEvent', 'handleAfterFeatureEvent', 'handleBeforeScenarioEvent', 'handleAfterScenarioEvent', 'handleBeforeStepEvent', 'handleStepResultEvent'];

var HookRunner = function () {
    function HookRunner(BaseListener, config) {
        (0, _classCallCheck3.default)(this, HookRunner);

        this.listener = BaseListener;

        this.beforeFeature = config.beforeFeature;
        this.beforeScenario = config.beforeScenario;
        this.beforeStep = config.beforeStep;
        this.afterFeature = config.afterFeature;
        this.afterScenario = config.afterScenario;
        this.afterStep = config.afterStep;

        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = (0, _getIterator3.default)(_get__('CUCUMBER_EVENTS')), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var fnName = _step.value;

                this.listener[fnName] = _get__('HookRunner').prototype[fnName].bind(this);
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally {
            try {
                if (!_iteratorNormalCompletion && _iterator.return) {
                    _iterator.return();
                }
            } finally {
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }
    }

    (0, _createClass3.default)(HookRunner, [{
        key: 'getListener',
        value: function getListener() {
            return this.listener;
        }
    }, {
        key: 'handleBeforeFeatureEvent',
        value: function handleBeforeFeatureEvent(event) {
            var feature = event.getUri ? event : event.getPayloadItem('feature');
            var exec = _get__('executeHooksWithArgs')(this.beforeFeature, [feature]);
            var done = arguments[1];
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.then(function () {
                return done();
            });
        }
    }, {
        key: 'handleBeforeScenarioEvent',
        value: function handleBeforeScenarioEvent(event) {
            var scenario = event.getUri ? event : event.getPayloadItem('scenario');
            var done = arguments[1];
            var exec = _get__('executeHooksWithArgs')(this.beforeScenario, [scenario]);
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.catch(function () {
                return done();
            });
        }
    }, {
        key: 'handleBeforeStepEvent',
        value: function handleBeforeStepEvent(event) {
            var step = event.getUri ? event : event.getPayloadItem('step');
            var done = arguments[1];
            var exec = _get__('executeHooksWithArgs')(this.beforeStep, [step]);
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.catch(function () {
                return done();
            });
        }
    }, {
        key: 'handleStepResultEvent',
        value: function handleStepResultEvent(event) {
            var stepResult = event.getStep ? event : event.getPayloadItem('stepResult');
            var done = arguments[1];
            var exec = _get__('executeHooksWithArgs')(this.afterStep, [stepResult]);
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.catch(function () {
                return done();
            });
        }
    }, {
        key: 'handleAfterScenarioEvent',
        value: function handleAfterScenarioEvent(event) {
            var scenario = event.getUri ? event : event.getPayloadItem('scenario');
            var done = arguments[1];
            var exec = _get__('executeHooksWithArgs')(this.afterScenario, [scenario]);
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.catch(function () {
                return done();
            });
        }
    }, {
        key: 'handleAfterFeatureEvent',
        value: function handleAfterFeatureEvent(event) {
            var feature = event.getUri ? event : event.getPayloadItem('feature');
            var done = arguments[1];
            var exec = _get__('executeHooksWithArgs')(this.afterFeature, [feature]);
            if (done.length === 0) {
                exec.then(function (res) {
                    if (typeof done === 'function') {
                        done(res);
                    }
                    return res;
                });
            }

            return exec.catch(function () {
                return done();
            });
        }
    }]);
    return HookRunner;
}();

exports.default = _get__('HookRunner');

function _getGlobalObject() {
    try {
        if (!!global) {
            return global;
        }
    } catch (e) {
        try {
            if (!!window) {
                return window;
            }
        } catch (e) {
            return this;
        }
    }
}

;
var _RewireModuleId__ = null;

function _getRewireModuleId__() {
    if (_RewireModuleId__ === null) {
        var globalVariable = _getGlobalObject();

        if (!globalVariable.__$$GLOBAL_REWIRE_NEXT_MODULE_ID__) {
            globalVariable.__$$GLOBAL_REWIRE_NEXT_MODULE_ID__ = 0;
        }

        _RewireModuleId__ = __$$GLOBAL_REWIRE_NEXT_MODULE_ID__++;
    }

    return _RewireModuleId__;
}

function _getRewireRegistry__() {
    var theGlobalVariable = _getGlobalObject();

    if (!theGlobalVariable.__$$GLOBAL_REWIRE_REGISTRY__) {
        theGlobalVariable.__$$GLOBAL_REWIRE_REGISTRY__ = (0, _create2.default)(null);
    }

    return __$$GLOBAL_REWIRE_REGISTRY__;
}

function _getRewiredData__() {
    var moduleId = _getRewireModuleId__();

    var registry = _getRewireRegistry__();

    var rewireData = registry[moduleId];

    if (!rewireData) {
        registry[moduleId] = (0, _create2.default)(null);
        rewireData = registry[moduleId];
    }

    return rewireData;
}

(function registerResetAll() {
    var theGlobalVariable = _getGlobalObject();

    if (!theGlobalVariable['__rewire_reset_all__']) {
        theGlobalVariable['__rewire_reset_all__'] = function () {
            theGlobalVariable.__$$GLOBAL_REWIRE_REGISTRY__ = (0, _create2.default)(null);
        };
    }
})();

var INTENTIONAL_UNDEFINED = '__INTENTIONAL_UNDEFINED__';
var _RewireAPI__ = {};

(function () {
    function addPropertyToAPIObject(name, value) {
        (0, _defineProperty2.default)(_RewireAPI__, name, {
            value: value,
            enumerable: false,
            configurable: true
        });
    }

    addPropertyToAPIObject('__get__', _get__);
    addPropertyToAPIObject('__GetDependency__', _get__);
    addPropertyToAPIObject('__Rewire__', _set__);
    addPropertyToAPIObject('__set__', _set__);
    addPropertyToAPIObject('__reset__', _reset__);
    addPropertyToAPIObject('__ResetDependency__', _reset__);
    addPropertyToAPIObject('__with__', _with__);
})();

function _get__(variableName) {
    var rewireData = _getRewiredData__();

    if (rewireData[variableName] === undefined) {
        return _get_original__(variableName);
    } else {
        var value = rewireData[variableName];

        if (value === INTENTIONAL_UNDEFINED) {
            return undefined;
        } else {
            return value;
        }
    }
}

function _get_original__(variableName) {
    switch (variableName) {
        case 'CUCUMBER_EVENTS':
            return CUCUMBER_EVENTS;

        case 'HookRunner':
            return HookRunner;

        case 'executeHooksWithArgs':
            return _wdioSync.executeHooksWithArgs;
    }

    return undefined;
}

function _assign__(variableName, value) {
    var rewireData = _getRewiredData__();

    if (rewireData[variableName] === undefined) {
        return _set_original__(variableName, value);
    } else {
        return rewireData[variableName] = value;
    }
}

function _set_original__(variableName, _value) {
    switch (variableName) {}

    return undefined;
}

function _update_operation__(operation, variableName, prefix) {
    var oldValue = _get__(variableName);

    var newValue = operation === '++' ? oldValue + 1 : oldValue - 1;

    _assign__(variableName, newValue);

    return prefix ? newValue : oldValue;
}

function _set__(variableName, value) {
    var rewireData = _getRewiredData__();

    if ((typeof variableName === 'undefined' ? 'undefined' : (0, _typeof3.default)(variableName)) === 'object') {
        (0, _keys2.default)(variableName).forEach(function (name) {
            rewireData[name] = variableName[name];
        });
    } else {
        if (value === undefined) {
            rewireData[variableName] = INTENTIONAL_UNDEFINED;
        } else {
            rewireData[variableName] = value;
        }

        return function () {
            _reset__(variableName);
        };
    }
}

function _reset__(variableName) {
    var rewireData = _getRewiredData__();

    delete rewireData[variableName];

    if ((0, _keys2.default)(rewireData).length == 0) {
        delete _getRewireRegistry__()[_getRewireModuleId__];
    }

    ;
}

function _with__(object) {
    var rewireData = _getRewiredData__();

    var rewiredVariableNames = (0, _keys2.default)(object);
    var previousValues = {};

    function reset() {
        rewiredVariableNames.forEach(function (variableName) {
            rewireData[variableName] = previousValues[variableName];
        });
    }

    return function (callback) {
        rewiredVariableNames.forEach(function (variableName) {
            previousValues[variableName] = rewireData[variableName];
            rewireData[variableName] = object[variableName];
        });
        var result = callback();

        if (!!result && typeof result.then == 'function') {
            result.then(reset).catch(reset);
        } else {
            reset();
        }

        return result;
    };
}

var _typeOfOriginalExport = typeof HookRunner === 'undefined' ? 'undefined' : (0, _typeof3.default)(HookRunner);

function addNonEnumerableProperty(name, value) {
    (0, _defineProperty2.default)(HookRunner, name, {
        value: value,
        enumerable: false,
        configurable: true
    });
}

if ((_typeOfOriginalExport === 'object' || _typeOfOriginalExport === 'function') && (0, _isExtensible2.default)(HookRunner)) {
    addNonEnumerableProperty('__get__', _get__);
    addNonEnumerableProperty('__GetDependency__', _get__);
    addNonEnumerableProperty('__Rewire__', _set__);
    addNonEnumerableProperty('__set__', _set__);
    addNonEnumerableProperty('__reset__', _reset__);
    addNonEnumerableProperty('__ResetDependency__', _reset__);
    addNonEnumerableProperty('__with__', _with__);
    addNonEnumerableProperty('__RewireAPI__', _RewireAPI__);
}

exports.__get__ = _get__;
exports.__GetDependency__ = _get__;
exports.__Rewire__ = _set__;
exports.__set__ = _set__;
exports.__ResetDependency__ = _reset__;
exports.__RewireAPI__ = _RewireAPI__;