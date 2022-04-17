<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Command\BidStrategyDeleteCommand;
use App\Application\Command\BidStrategyUpdateCommand;
use App\Application\DTO\BidStrategyDeleteDTO;
use App\Application\DTO\BidStrategyUpdateDTO;
use App\Application\Exception\ValidationException;
use App\Domain\ValueObject\PaymentCalculatorConfig;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfon