<?php

namespace App\Application\Controller;

use App\Domain\Entity\EmailEvent;
use App\Domain\Repository\EmailEventRepositoryInterface as Events;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrackingController extends AbstractController
{
    #[Route('/api/open/{tid}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/open/{tid}',
        summary: 'Track email open',
        description: 'Returns a 1x1 transparent PNG tracking pixel and logs an "open" event for the given tracking ID.',
        tags: ['Tracking'],
        parameters: [
        new OA\Parameter(
            name: 'tid',
            in: 'path',
            required: true,
            description: 'Unique tracking ID for the email',
            schema: new OA\Schema(type: 'string', example: 'abc123')
        ),
        new OA\Parameter(
            name: 'c',
            in: 'query',
            required: false,
            description: 'Optional campaign ID',
            schema: new OA\Schema(type: 'string', example: 'cmp-2025')
        )
        ],
        responses: [
        new OA\Response(
            response: 200,
            description: '1x1 transparent PNG returned',
            content: new OA\MediaType(
                mediaType: 'image/png',
                schema: new OA\Schema(type: 'string', format: 'binary')
            )
        )
        ]
    )]
    public function open(string $tid, Request $req, Events $events): Response
    {
        $cid = $req->query->get('c');
        $e = new EmailEvent();
        $e->setTrackingId($tid);
        $e->setType('open');
        $e->setOccuredAt(new \DateTimeImmutable());
        $e->setMeta($cid);
        $events->log($e);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=');
        return new Response($png, 200, ['Content-Type' => 'image/png','Cache-Control' => 'no-store']);
    }

    #[Route('/r/{tid}', methods: ['GET'])]
    #[OA\Get(
        path: '/r/{tid}',
        summary: 'Track email link click',
        description: 'Logs a "click" event for the given tracking ID and redirects the user to the original URL.',
        tags: ['Tracking'],
        parameters: [
           new OA\Parameter(
               name: 'tid',
               in: 'path',
               required: true,
               description: 'Unique tracking ID for the email',
               schema: new OA\Schema(type: 'string', example: 'abc123')
           ),
           new OA\Parameter(
               name: 'u',
               in: 'query',
               required: true,
               description: 'Encoded destination URL where the user will be redirected',
               schema: new OA\Schema(type: 'string', format: 'uri', example: 'https%3A%2F%2Fexample.com')
           ),
           new OA\Parameter(
               name: 'c',
               in: 'query',
               required: false,
               description: 'Optional campaign ID',
               schema: new OA\Schema(type: 'string', example: 'cmp-2025')
           )
        ],
        responses: [
           new OA\Response(
               response: 302,
               description: 'Redirect to the original destination URL',
               headers: [
                   new OA\Header(
                       header: 'Location',
                       description: 'The destination URL',
                       schema: new OA\Schema(type: 'string', format: 'uri', example: 'https://example.com')
                   )
               ]
           )
        ]
    )]
    public function redirectTo(string $tid, Request $req, Events $events): Response
    {


        $url = $req->query->get('u', '/');
        $cid = $req->query->get('c');
        $e = new EmailEvent();
        $e->setTrackingId($tid);
        $e->setType('click');
        $e->setOccuredAt(new \DateTimeImmutable());
        $e->setMeta($cid);
        $events->log($e);
        return new Response('', 302, ['Location' => $url]);
    }
}
