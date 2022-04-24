<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Command\EventUpdateCommand;
use App\Application\DTO\ClickEventUpdateDTO;
use App\Application\DTO\ConversionEventUpdateDTO;
use App\Application\DTO\EventUpdateDTO;
use App\Application\DTO\ViewEventUpdateDTO;
use App\Application\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class EventController extends AbstractController
{
    private EventUpdateCommand $eventUpdateCommand;

    private LoggerInterface $logger;

    public function __construct(EventUpdateCommand $eventUpdateCommand, LoggerInterface $logger)
    {
        $this->eventUpdateCommand = $eventUpdateCommand;
        $this->logger = $logger;
    }

    private function parseRequest(Request $request, string $dto): EventUpdateDTO
    {
        $input = json_decode($request->getContent(), true);
     