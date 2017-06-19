'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = getFilePath;

var _path = require('path');

var _path2 = _interopRequireDefault(_path);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var FILE_EXTENSION_REGEX = /\.[0-9a-z]+$/i;

/**
 * Resolves the given path into a absolute path and appends the default filename as fallback when the provided path is a directory.
 * @param  {String} logPath         relative file or directory path
 * @param  {String} defaultFilename default file name when filePath is a directory
 * @return {String}                 absolute file path
 */
function getFilePath(filePath, defaultFilename) {
    var absolutePath = _path2.default.join(process.cwd(), filePath);

    // test if we already have a file (e.g. selenium.txt, .log, log.txt, etc.)
    // NOTE: path.extname doesn't work to detect a file, cause dotfiles are reported by node to have no extension
    if (!FILE_EXTENSION_REGEX.test(_path2.default.basename(absolutePath))) {
        absolutePath = _path2.default.join(absolutePath, defaultFilename);
    }

    return absolutePath;
}
