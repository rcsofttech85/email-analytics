<?php

namespace App\Tests\Unit\Service;

use App\Domain\Repository\EmailEventRepositoryInterface;
use App\Domain\Repository\EmailRepositoryInterface;
use App\Domain\Service\AnalyticsService;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class AnalyticsServiceTest extends TestCase
{
    private AnalyticsService $service;
    private $eventsMock;
    private $cacheMock;
    private $cacheItemMock;
    private $emailMock;

    protected function setUp(): void
    {

        $this->eventsMock = $this->createMock(EmailEventRepositoryInterface::class);
        $this->emailMock = $this->createMock(EmailRepositoryInterface::class);
        $this->cacheItemMock = $this->createMock(CacheItemInterface::class);
        $this->cacheMock = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheMock->method('getItem')->willReturn($this->cacheItemMock);
        $this->cacheMock->method('save')->willReturn(true);
        $this->service = new AnalyticsService($this->emailMock, $this->eventsMock, $this->cacheMock);
    }

    public function testCampaignStatsCacheHit(): void
    {
        $data = ['campaignId' => 'cmp-001','opens' => 2,'clicks' => 1,'openRate' => 50,'clickRate' => 25];
        $this->cacheItemMock->method('isHit')->willReturn(true);
        $this->cacheItemMock->method('get')->willReturn($data);

        $result = $this->service->campaignStats('cmp-001');
        $this->assertEquals($data, $result);
    }

    public function testCampaignStatsCacheMiss(): void
    {
        $this->cacheItemMock->method('isHit')->willReturn(false);


        $this->eventsMock->method('countByCampaignAndType')->willReturnMap([
            ['cmp-001','open', 3],
            ['cmp-001','click', 2],
        ]);
        $this->eventsMock->method('countRecipientsByCampaign')->with('cmp-001')->willReturn(5);

        $result = $this->service->campaignStats('cmp-001');

        $expected = [
            'campaignId' => 'cmp-001',
            'opens' => 3,
            'clicks' => 2,
            'openRate' => 60.0,
            'clickRate' => 40.0,
        ];

        $this->assertEquals($expected, $result);
    }
}
