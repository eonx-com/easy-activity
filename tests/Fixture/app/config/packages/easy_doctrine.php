<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use stdClass;
use Symfony\Config\EasyDoctrineConfig;

return static function (EasyDoctrineConfig $easyDoctrineConfig): void {
    $easyDoctrineConfig->easyErrorHandler()
        ->enabled(false);

    $easyDoctrineConfig
        ->deferredDispatcherEntities([
            stdClass::class,
        ]);
};
