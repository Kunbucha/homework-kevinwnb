<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Command\PaymentFetchCommand;
use App\Application\Command\ReportCalculateCommand;
use App\Application\Exception\FetchingException;
use App\Application\Exception\ReportNotFoundException;
use App\Application\Exception\ReportNotCompleteException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaymentController extends Abst