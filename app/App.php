<?php

namespace Point;

use DI\ContainerBuilder;
use DI\Bridge\Slim\App as DIBridge;

class App extends DIBridge
{
    protected function configureContainer(ContainerBuilder $builder)
    {
        $builder->addDefinitions([
            // Turn off on production
            'settings.displayErrorDetails' => false,
            // 'settings.displayErrorDetails' => true,
        ]);

        $builder->addDefinitions(__DIR__ . '/container.php');
    }
}