<?php

namespace App\Application\Controller;

use App\Application\DTO\EmailDTO;
use App\Application\Message\Command\SendEmailCommand;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailController extends AbstractController
{
    #[Route('/api/emails', methods: ['POST'])]
    #[OA\Post(
        path: '/api/emails',
        summary: 'Queue an email for sending',
        description: 'Accepts email details and queues it for delivery asynchronously via Messenger.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                new OA\Property(property: 'to', type: 'string', description: 'Recipient email address', example: 'test@example.com'),
                new OA\Property(property: 'subject', type: 'string', description: 'Email subject', example: 'Welcome'),
                new OA\Property(property: 'htmlBody', type: 'string', description: 'HTML content of the email, supports {{tracked_link}} placeholder', example: '<p>Hello {{tracked_link}}</p>'),
                new OA\Property(property: 'campaignId', type: 'string', description: 'Optional campaign identifier', example: 'cmp-aug-2025'),
                new OA\Property(property: 'redirectUrl', type: 'string', description: 'Optional redirect URL for tracked links', example: 'https://example.com/welcome'),
            ]
            )
        ),
        responses: [
        new OA\Response(response: 202, description: 'Email successfully queued'),
        new OA\Response(
            response: 422,
            description: 'Validation failed',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'errors', type: 'string', description: 'Validation error messages')
                ]
            )
        )
    ]
    )]
    public function queue(Request $req, ValidatorInterface $v, MessageBusInterface $bus): JsonResponse
    {
        $d = json_decode($req->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $dto = new EmailDTO($d['to'], $d['subject'], $d['htmlBody'], $d['campaignId'] ?? null, $d['redirectUrl'] ?? null);
        $errors = $v->validate($dto);
        if (count($errors)) {
            return new JsonResponse(['errors' => (string)$errors], 422);
        }
        $bus->dispatch(new SendEmailCommand($dto));
        return new JsonResponse(['status' => 'queued'], 202);
    }
}
