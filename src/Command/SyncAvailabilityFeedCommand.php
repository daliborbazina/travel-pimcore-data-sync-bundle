<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Command;

use DaliborBazina\TravelPimcoreDataSyncBundle\Sync\AvailabilitySyncService;
use DaliborBazina\TravelPimcoreDataSyncBundle\Sync\SyncContext;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function ctype_digit;
use function dirname;
use function is_file;
use function is_readable;
use function sprintf;
use function trim;

#[AsCommand(
    name: 'travel:sync:availability',
    description: 'Synchronizes supplier availability feed data into Pimcore.',
)]
final class SyncAvailabilityFeedCommand extends Command
{
    public function __construct(
        private readonly AvailabilitySyncService $availabilitySyncService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $defaultFixturePath = dirname(__DIR__, 2).'/fixtures/supplier-travel-availability-feed.json';

        $this
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Path to the supplier availability feed JSON file.',
                $defaultFixturePath,
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Runs the availability synchronization without persisting any changes.',
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Limits the number of processed availability records.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filePath = (string) $input->getOption('file');
        $dryRun = (bool) $input->getOption('dry-run');

        $limitOption = $input->getOption('limit');
        $limit = null;

        if (null !== $limitOption) {
            $limitValue = trim((string) $limitOption);

            if ('' === $limitValue) {
                $io->error('The --limit option must not be empty.');

                return Command::FAILURE;
            }

            if (!ctype_digit($limitValue)) {
                $io->error('The --limit option must be a positive integer.');

                return Command::FAILURE;
            }

            $limit = (int) $limitValue;

            if ($limit < 1) {
                $io->error('The --limit option must be greater than 0.');

                return Command::FAILURE;
            }
        }

        if ('' === trim($filePath)) {
            $io->error('The availability feed file path must not be empty.');

            return Command::FAILURE;
        }

        if (!is_file($filePath)) {
            $io->error(sprintf('The availability feed file "%s" was not found.', $filePath));

            return Command::FAILURE;
        }

        if (!is_readable($filePath)) {
            $io->error(sprintf('The availability feed file "%s" is not readable.', $filePath));

            return Command::FAILURE;
        }

        $io->title('Supplier Availability Feed Sync');
        $io->definitionList(
            ['Catalog feed file' => $filePath],
            ['Dry run' => $dryRun ? 'yes' : 'no'],
            ['Limit' => null !== $limit ? (string) $limit : 'none'],
        );

        try {
            $context = new SyncContext(
                filePath: $filePath,
                dryRun: $dryRun,
                limit: $limit,
            );

            $report = $this->availabilitySyncService->sync($context);
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->section('Sync Summary');
        $io->definitionList(
            ['Records read' => (string) $report->getRecordsRead()],
            ['Records selected' => (string) $report->getRecordsSelected()],
            ['Records processed' => (string) $report->getProcessed()],
            ['Records succeeded' => (string) $report->getRecordsSucceeded()],
            ['Records invalid' => (string) $report->getRecordsInvalid()],
            ['Records failed' => (string) $report->getRecordsFailed()],
            ['Travel offers updated' => (string) $report->getTravelOffersUpdated()],
        );
        $io->success('Supplier availability feed synchronization finished.');

        return Command::SUCCESS;
    }
}
