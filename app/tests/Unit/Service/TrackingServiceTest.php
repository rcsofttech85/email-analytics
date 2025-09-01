<?php

namespace App\Tests\Unit\Service;

use App\Domain\Service\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackingServiceTest extends TestCase
{
    private TrackingService $service;

    protected function setUp(): void
    {
        // Provide a base URL for the service
        $this->service = new TrackingService('https://app.test');
    }

    public function testGenerateTrackingId(): void
    {
        $tid = $this->service->generateTrackingId();
        $this->assertIsString($tid);
        $this->assertNotEmpty($tid);
    }

    public function testPixelUrl(): void
    {
        $tid = 'abc123';
        $cid = 'cmp-001';
        $pixel = $this->service->pixelUrl($tid, $cid);
        $this->assertStringContainsString($tid, $pixel);
        $this->assertStringContainsString('/api/open/', $pixel);
    }

    public function testRedirectUrl(): void
    {
        $tid = 'abc123';
        $target = 'https://example.com';
        $cid = 'cmp-001';

        $redirect = $this->service->redirectUrl($tid, $target, $cid);

        $this->assertStringContainsString($tid, $redirect);
        $this->assertStringContainsString(urlencode($target), $redirect);
        $this->assertStringContainsString($cid, $redirect);
        $this->assertStringStartsWith('https://app.test', $redirect);
    }

    public function testInjectPixelAndTrackedLink(): void
    {
        $html = '<p>Hello {{tracked_link}}</p>';
        $pixelUrl = 'https://app.test/api/open/123';

        $htmlWithPixel = $this->service->injectPixel($html, $pixelUrl);
        $this->assertStringContainsString('<img', $htmlWithPixel);
        $this->assertStringContainsString($pixelUrl, $htmlWithPixel);

        $trackedLink = 'https://app.test/r/123?u=https%3A%2F%2Fexample.com';
        $htmlWithLink = $this->service->injectTrackedLink($html, $trackedLink);

        $this->assertStringContainsString($trackedLink, $htmlWithLink);

    }
}
