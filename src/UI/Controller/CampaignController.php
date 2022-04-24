<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Command\CampaignDeleteCommand;
use App\Application\Command\CampaignUpdateCommand;
use App\Application\DTO\CampaignDeleteDTO;
use App\Application\DTO\CampaignUpdateDTO;
use App\Application\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CampaignController extends AbstractController
{
    private CampaignUpdateCommand $updateCommand;

  