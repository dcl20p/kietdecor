<?php
declare(strict_types=1);

namespace GeneralTraits\Controller;

trait General
{
    /**
     * escape string
     *
     * @param string $str
     * @return string
     */
    protected function escapeString(string $str): string
    {
        return trim(htmlspecialchars($str));
    }
}