<?php

namespace App\Domain\Service;

use Symfony\Component\Uid\Uuid;

class TrackingService
{
    public function __construct(private string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function generateTrackingId(): string
    {
        return Uuid::v7()->toRfc4122();
    }

    public function pixelUrl(string $tid, ?string $campaignId): string
    {
        $q = http_build_query([

            'c' => $campaignId,
        ]);
        return $this->baseUrl . '/api/open/' . $tid. '?' . $q;
    }

    public function redirectUrl(string $tid, string $target, ?string $campaignId): string
    {
        $q = http_build_query([
            'u' => $target,
            'c' => $campaignId,
        ]);

        return $this->baseUrl . '/r/' . $tid . '?' . $q;
    }

    public function injectPixel(string $html, string $pixelUrl): string
    {
        return $html . sprintf(
            '<img src="%s" width="1" height="1" alt="" style="display:none;">',
            htmlspecialchars($pixelUrl, ENT_QUOTES)
        );
    }

    public function injectTrackedLink(string $html, string $url): string
    {
        return str_replace(
            '{{tracked_link}}',
            '<a href="' . htmlspecialchars($url, ENT_QUOTES) . '">Click Here</a>',
            $html
        );
    }
}
