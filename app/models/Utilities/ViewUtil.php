<?php

namespace Models\Utilities;

class ViewUtil
{
    /**
     * Create get link to download file
     *
     * @param string $baseLink
     * @return string
     */
    public static function getDownloadLink(string $baseLink = ''): string
    {
        $baseLink = rtrim($baseLink, '/');
        
        return str_replace([
            'https://manager.','https://www.manager.',
            'https://customer.','https://www.customer.',
            'https://','https://www.',
        ], 'https://www.media.', $baseLink);
    }
}
