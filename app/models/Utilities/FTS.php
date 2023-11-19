<?php
namespace Models\Utilities;

class FTS
{
    /**
     * Keyword to add for prevent min fulltext index search
     * @var string
     */
    const FT_CHAR_PLUS = '_';
    /**
     * MySQL boolean wildcard
     * @var array
     */
    const SPEC_CHAR = ['@', '-', '+', '~', '"', '(', ')', '>', '<', '*', '.'];
    /**
     * Keyword to prevent boolean wildcard
     * @var array
     */
    const SPEC_REPL = ['__1', '__2', '__3', '__4', '__5', '__6', '__7', '__8', '__9', '__10', '__11'];

    /**
     * Remove Fulltext Search special chars
     * @param string $str
     * @return string
     */
    public static function replaceFTSpecialChar(string $str = ''): string
    {
        return str_replace(self::SPEC_CHAR, self::SPEC_REPL, trim($str));
    }

    /**
     * Search full text origin
     *
     * @param string $str
     * @return string
     */
    public static function searchFullTextOrigin(string $str = ''): string
    {
        if (empty($str)) return '';

        $explode = explode(' ', $str);
        $explode = array_map(function($item) {
            $item = trim($item);
            return "+{$item}*";
        }, $explode);

        return implode(" ", $explode);
    }
}