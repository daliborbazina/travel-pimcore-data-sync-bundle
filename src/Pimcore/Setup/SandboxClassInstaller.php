<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Setup;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

final readonly class SandboxClassInstaller
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public function installOrUpdate(): void
    {
        $projectDir = $this->kernel->getProjectDir();
        $definitionsDir = $projectDir.'/config/pimcore/classes';

        $requiredFiles = [
            'definition_Destination.php',
            'definition_Hotel.php',
            'definition_TravelOffer.php',
            'definition_TravelOfferAvailability.php',
        ];

        foreach ($requiredFiles as $file) {
            $path = $definitionsDir.'/'.$file;

            if (!is_file($path)) {
                throw new \RuntimeException(
                    sprintf(
                        'Missing Pimcore class definition file: %s',
                        $path
                    )
                );
            }
        }

        $process = new Process([
            PHP_BINARY,
            $projectDir.'/bin/console',
            'pimcore:deployment:classes-rebuild',
            '--create-classes',
            '--force',
            '--no-interaction',
        ], $projectDir);

        $process->setTimeout(300);
        $process->mustRun();
    }
}
