<?php

declare(strict_types=1);

namespace App\UI\Cli;

use App\Application\Photo\ImportPhotos\Exception\PhotoImportFailedException;
use App\Application\Photo\ImportPhotos\Exception\PhoenixTokenNotConfiguredException;
use App\Application\Photo\ImportPhotos\Exception\UserNotFoundException;
use App\Application\Photo\ImportPhotos\ImportPhotosCommand as ImportPhotosUseCaseCommand;
use App\Application\Photo\ImportPhotos\ImportPhotosUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:photos:import:phoenix',
    description: 'Import photos from Phoenix provider for selected user',
)]
final class ImportPhoenixPhotosCommand extends Command
{
    public function __construct(
        private readonly ImportPhotosUseCase $importPhotosUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user-id', InputArgument::REQUIRED, 'User id to import photos for')
            ->addOption('token', null, InputOption::VALUE_OPTIONAL, 'Phoenix token override');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userId = (int) $input->getArgument('user-id');
        $tokenOverride = $input->getOption('token');
        $token = is_string($tokenOverride) ? $tokenOverride : null;

        try {
            $result = $this->importPhotosUseCase->execute(
                new ImportPhotosUseCaseCommand($userId, 'phoenix', $token)
            );

            $io->success(sprintf(
                'Phoenix import finished: %d imported, %d skipped.',
                $result->importedCount,
                $result->skippedCount
            ));

            return Command::SUCCESS;
        } catch (PhoenixTokenNotConfiguredException $e) {
            $io->error('No Phoenix token configured for this user. Save it on profile page or pass --token.');
        } catch (UserNotFoundException $e) {
            $io->error('User not found.');
        } catch (PhotoImportFailedException $e) {
            $io->error('Phoenix import failed. Check endpoint availability and token.');
        }

        return Command::FAILURE;
    }
}
