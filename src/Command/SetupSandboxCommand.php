<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Command;

use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Setup\SandboxClassInstaller;
use DaliborBazina\TravelPimcoreDataSyncBundle\Pimcore\Setup\SandboxDataSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'travel:sandbox:setup',
    description: 'Rebuilds Pimcore class definitions and seeds sandbox travel data.',
)]
final class SetupSandboxCommand extends Command
{
    public function __construct(
        private readonly SandboxClassInstaller $classInstaller,
        private readonly SandboxDataSeeder $dataSeeder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('classes-only', null, InputOption::VALUE_NONE, 'Only rebuild/create Pimcore classes.')
            ->addOption('seed-only', null, InputOption::VALUE_NONE, 'Only seed sandbox data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $classesOnly = (bool) $input->getOption('classes-only');
        $seedOnly = (bool) $input->getOption('seed-only');

        if ($classesOnly && $seedOnly) {
            $io->error('Use either --classes-only or --seed-only, not both.');

            return Command::INVALID;
        }

        if (!$seedOnly) {
            $io->section('Installing / updating Pimcore class definitions');
            $this->classInstaller->installOrUpdate();
            $io->success('Class definitions are synced.');
        }

        if (!$classesOnly) {
            $io->section('Seeding sandbox data');
            $this->dataSeeder->seed();
            $io->success('Sandbox data seeded.');
        }

        $io->success('Sandbox setup finished.');

        return Command::SUCCESS;
    }
}
