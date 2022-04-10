<?php

declare(strict_types=1);

namespace App\UI\Command;

use App\Application\Command\ReportCalculateCommand;
use App\Application\Command\ReportFetchCompletedCommand;
use App\Application\Exception\FetchingException;
use App\Lib\DateTimeHelper;
use App\Lib\Exception\DateTimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PaymentsCalculateCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'ops:payments:calculate';

    private ReportFetchCompletedCommand $reportFetchCommand;

    private ReportCalculateCommand $reportCalculateCommand;

    public function __construct(
        ReportFetchCompletedCommand $reportFetchCommand,
        ReportCalculateCommand $reportCalculateCommand,
        string $name = null
    ) {
        parent::__construct($name);
        $this->reportFetchCommand = $reportFetchCommand;
        $this->reportCalculateCommand = $reportCalculateCommand;
    }

    protected function configure()
    {
        $this
            ->setDescription('Calculates payments for events')
            ->addArgument('date', InputArgument::OPTIONAL, 'Report date or timestamp')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force calculation of incomplete report');
    }

