<?php

declare(strict_types=1);

namespace DaliborBazina\TravelPimcoreDataSyncBundle\Command;

use DaliborBazina\TravelPimcoreDataSyncBundle\Publish\PublishPreviewService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function ctype_digit;
use function trim;

#[AsCommand(
    name: 'travel:publish:preview',
    description: 'Builds a publish payload preview from synchronized Pimcore travel offers.',
)]
final class PreviewPublishPayloadCommand extends Command
{
    public function __construct(
        private readonly PublishPreviewService $publishPreviewService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Limits the number of exported payload records.',
            )
            ->addOption(
                'pretty',
                null,
                InputOption::VALUE_NONE,
                'Outputs pretty-printed JSON.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pretty = (bool) $input->getOption('pretty');
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

        try {
            $output->writeln(
                $this->publishPreviewService->buildPreviewAsJson($limit, $pretty),
            );

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
