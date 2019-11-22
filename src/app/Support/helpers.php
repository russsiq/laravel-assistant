<?php

/**
 * minreq - Get info about minimum requirements.
 */

use RuntimeException;

if (! function_exists('minreq')) {
    /**
     * Get info about minimum requirements.
     *
     * @param  string    $requirement
     *
     * @return string
     *
     * @throws RuntimeException  Unknown `requirement` argument.
     */
    function minreq(string $requirement)
    {
        switch ($requirement) {
            case 'php':
                return version_compare(phpversion(), '7.2.2', '>=') ? phpversion() : false; break;
            case 'pdo':
                // return (extension_loaded('PDO') and extension_loaded('pdo_mysql') and class_exists('PDO')) ? \DB::select(\DB::raw("select version()"))[0]->{'version()'} : false; break;
                return (extension_loaded('PDO') and class_exists('PDO')) ?? false; break;
            case 'ssl':
                return OPENSSL_VERSION_TEXT ?? false; break;
            case 'gd':
                return gd_info() ? gd_info()['GD Version'] : false; break;
            case 'finfo':
                return function_exists('finfo_open') ?? false; break; // PHP >= 7.2.0
            case 'mb':
                return extension_loaded('mbstring') ?? false; break;
            case 'tokenizer':
                return extension_loaded('tokenizer') ?? false; break;
            case 'ctype':
                return extension_loaded('ctype') ?? false; break;
            case 'json':
                return function_exists('json_encode') ?? false; break;
            case 'zlib':
                return (extension_loaded('zlib') and function_exists('ob_gzhandler')) ?? false; break;
            case 'xml':
                return function_exists('xml_parser_create') ?? false; break;
            case 'curlLibrary':
                return function_exists('curl_init') ?? false; break;
            case 'zipLibrary':
                return class_exists('ZipArchive') ?? false; break;
        }

        throw new RuntimeException('Unknown requirement argument.');
    }
}
