<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

class EmailControllerTest extends BaseWebTestCase
{
    public function testQueueEmail(): void
    {
        $payload = [
            'to' => 'test@example.com',
            'subject' => 'Hello',
            'htmlBody' => '<p>Click {{tracked_link}}</p>',
            'campaignId' => 'cmp-test',
            'redirectUrl' => 'https://example.com'
        ];

        $this->client->request('POST', '/api/emails', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
        $this->assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('queued', $data['status']);
    }
}
