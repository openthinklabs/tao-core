const fs = require('fs');

function readDir(path) {
    return new Promise((resolve, reject) => {
        fs.readdir(path, (err, files) => {
            if (err) {
                reject(err);
            } else {
                resolve(files);
            }
        });
    });
}

/**
 * Removes file from the specified folder
 * @param {String} path - target file path
 * @return {Promise<void>}
 */
function removeFile(path) {
    return new Promise((resolve, reject) => {
        fs.unlink(path, (err) => {
            if (err) {
                reject(err);
            } else {
                resolve(null);
            }
        });
    });
}

/**
 * Reads file content
 * @param {String} path - target file path
 * @param {String} encoding
 * @return {Promise<void>}
 */
function readFile(path, encoding) {
    return new Promise((resolve, reject) => {
        fs.readFile(path, encoding,(err, data) => {
            if (err) {
                reject(err);
            } else {
                resolve(data);
            }
        });
    });
}

/**
 * Tries to find files in the folder, returning only after at least one file is present
 * @param {String} path - target folder
 * @return {Promise<Array<String>>} found file names
 */
function getFiles(path) {
    const timeout = 5000;
    const delay = 100;

    const getFiles = resolve => {
        readDir(path).then(
            files => {
                if (files.length) {
                    resolve(files);
                } else {
                    setTimeout(() => getFiles(resolve), delay);
                }
            }
        );
    };

    return new Promise(resolve => {
        setTimeout(
            () => resolve(false),
            timeout
        );
        return getFiles(resolve);
    });
}

module.exports = {
    getFiles,
    removeFile,
    readFile,
    readDir
}