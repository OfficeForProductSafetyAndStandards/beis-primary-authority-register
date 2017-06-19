'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
var isElements = exports.isElements = function isElements(result) {
    return typeof result.selector === 'string' && Array.isArray(result.value) && result.value.length && typeof result.value[0].ELEMENT !== 'undefined';
};

var is$$ = exports.is$$ = function is$$(result) {
    return Array.isArray(result) && !!result.length && !!result[0] && result[0].ELEMENT !== undefined;
};