<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('travel_pimcore_data_sync_bundle');
    }
}
