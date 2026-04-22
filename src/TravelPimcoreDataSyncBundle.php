<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle;

use DaliborBazina\TravelPimcoreDataSyncBundle\DependencyInjection\TravelPimcoreDataSyncExtension;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class TravelPimcoreDataSyncBundle extends AbstractPimcoreBundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new TravelPimcoreDataSyncExtension();
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
