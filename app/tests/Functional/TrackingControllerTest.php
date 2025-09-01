<?php

namespace App\Tests\Functional;

use App\Domain\Entity\EmailEvent;
use App\Domain\Repository\EmailEventRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class TrackingControllerTest extends BaseWebTestCase
{
    private $mockEvents;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockEvents = $this->createMock(EmailEventRepositoryInterface::class);

        self::getContainer()->set(EmailEventRepositoryInterface::class, $this->mockEvents);
    }

    public function testPixelOpen(): void
    {
        $tid = bin2hex(random_bytes(8));


        $this->mockEvents->expects($this->once())
            ->method('log')
            ->with($this->callback(function (EmailEvent $event) use ($tid) {
                return $event->getTrackingId() === $tid && $event->getType() === 'open';
            }));

        $this->client->request('GET', "/api/open/$tid", ['c' => 'cmp-test']);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }

    public function testRedirectClick(): void
    {
        $tid = bin2hex(random_bytes(8));
        $url = 'https://example.com';


        $this->mockEvents->expects($this->once())
            ->method('log')
            ->with($this->callback(function (EmailEvent $event) use ($tid) {
                return $event->getTrackingId() === $tid && $event->getType() === 'click';
            }));

        $this->client->request(
            'GET',
            "/r/$tid",
            ['u' => $url, 'c' => 'cmp-test']
        );

        $response = $this->client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals($url, $response->headers->get('Location'));
    }
}
