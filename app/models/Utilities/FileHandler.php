<?php
namespace Models\Utilities;

use Laminas\Config\Writer\PhpArray;

class FileHandler 
{
    /**
     * Write array to php file
     * @param string $path
     * @param array $data
     * @return void
     */
    public static function writePhpArray(string $path, array $data, bool $parseArrayObj = false): void
    {
        $writer = new PhpArray();
        $writer->setUseBracketArraySyntax(true);
        $writer->toFile($path, $data);

        if ($parseArrayObj) @file_put_contents($path, str_replace(
            'ArrayObject::__set_state', 'new \ArrayObject', 
            @file_get_contents($path)
        ));

        self::clearBuildCache($path);
        unset($writer);
    }

    /**
     * Clear php cache file
     * @param string $path
     * @return boolean
     */
    public static function clearBuildCache(string $path): bool
    {
        if (function_exists('opcache_invalidate')
            && strlen(ini_get('opcache.restrict_api')) < 1
        ) {
            opcache_invalidate($path, true);
            return true;
        } else if (function_exists('apc_compile_file')) {
            apc_compile_file($path);
            return true;
        }
        return false;
    }
}