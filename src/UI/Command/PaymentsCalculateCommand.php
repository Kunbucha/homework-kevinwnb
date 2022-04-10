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

class Payment