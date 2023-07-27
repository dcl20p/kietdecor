<?php
namespace Group;

/**
 * @moduleTitle: Group
 */
class Module
{
    const VERSION = '3.0.0dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}

