<?php

namespace App\Application\Controller;

use App\Domain\Service\AnalyticsService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController extends AbstractController
{
    #[Route('/api/analytics/{campaignId}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/analytics/{campaignId}',
        summary: 'Get analytics for a campaign',
        description: 'Returns email tracking statistics (opens, clicks, etc.) for a given campaign ID.',
        tags: ['Analytics'],
        parameters: [
        new OA\Parameter(
            name: 'campaignId',
            in: 'path',
            required: true,
            description: 'The ID of the campaign to fetch analytics for',
            schema: new OA\Schema(type: 'string')
        ),
        ],
        responses: [
        new OA\Response(
            response: 200,
            description: 'Campaign analytics',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'opens', type: 'integer', example: 42),
                    new OA\Property(property: 'clicks', type: 'integer', example: 18),
                    new OA\Property(property: 'campaignId', type: 'string', example: 'cmp-123'),
                ]
            )
        ),
        new OA\Response(response: 404, description: 'Campaign not found')
        ]
    )]
    public function campaign(string $campaignId, AnalyticsService $svc): JsonResponse
    {
        return new JsonResponse($svc->campaignStats($campaignId));
    }
}
