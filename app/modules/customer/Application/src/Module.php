<?php

declare(strict_types=1);

namespace Application;

use Exception;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Models\Utilities\AppUtilities;

/**
 * @moduleTitle: Dashboard
 */
class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    /**
     * This method is called once the MVC bootstrapping is complete
     *
     * @param MvcEvent $event
     * @return void
     */
    public function onBootstrap(MvcEvent $event): void
    {
        $eventManager = $event->getApplication()->getEventManager();
        $sharedManager = $eventManager->getSharedManager();

        $sharedManager->attach(
            Application::class,
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'onZFExceptionError']
        );

        $eventManager->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'onZFExceptionError']
        );
    }

    public function onZFExceptionError(MvcEvent $event): void
    {
        AppUtilities::logErrors($event);
    }
}