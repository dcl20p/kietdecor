<?php

declare(strict_types=1);

namespace Application;

use Exception;
use Laminas\Http\PhpEnvironment\Request;
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

        /* $eventManager->attach(
            MvcEvent::EVENT_ROUTE, 
            [$this, 'onRoute'],
            100
        ); */
    }

    public function onZFExceptionError(MvcEvent $event): void
    {
        AppUtilities::logErrors($event);
    }

    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();

        if ($request instanceof Request) {
            $params = $request->getQuery();
            $paramsToRemove = [];

            foreach ($params as $key => $value) {
                // Lưu trữ các tham số cần loại bỏ
                if ($value === '') {
                    $paramsToRemove[] = $key;
                }
            }

            // Loại bỏ các tham số đã được lưu trữ
            foreach ($paramsToRemove as $key) {
                $request->getQuery()->offsetUnset($key);
            }
        }
    }
}