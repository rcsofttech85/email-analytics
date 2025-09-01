<?php

namespace App\Domain\Service;

use App\Domain\Repository\EmailEventRepositoryInterface;
use App\Domain\Repository\EmailRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;

class AnalyticsService
{
    public function __construct(private EmailRepositoryInterface $emailRepo, private EmailEventRepositoryInterface $events, private CacheItemPoolInterface $analyticsCache)
    {
    }
    public function campaignStats(string $cid): array
    {
        $item = $this->analyticsCache->getItem('stats_'.$cid);
        if ($item->isHit()) {
            return $item->get();
        }
        $opens = $this->events->countByCampaignAndType($cid, 'open');

        $clicks = $this->events->countByCampaignAndType($cid, 'click');
        $recipients = max(1, $this->events->countRecipientsByCampaign($cid));
        $data = ['campaignId' => $cid,'opens' => $opens,'clicks' => $clicks,'openRate' => round($opens / $recipients * 100, 2),'clickRate' => round($clicks / $recipients * 100, 2)];
        $item->set($data)->expiresAfter(60);
        $this->analyticsCache->save($item);
        return $data;
    }

    public function getDashboardData(): array
    {
        
        $totalEmails = $this->emailRepo->count([]);
        $totalOpens = $this->events->countByType('open');
        $totalClicks = $this->events->countByType('click');

        $openRate = $totalEmails > 0 ? round(($totalOpens / $totalEmails) * 100, 2) : 0;
        $clickRate = $totalEmails > 0 ? round(($totalClicks / $totalEmails) * 100, 2) : 0;

        $campaigns = $this->emailRepo->getCampaignStats();
        return [
            'totalEmails' => $totalEmails,
            'openRate' => $openRate,
            'clickRate' => $clickRate,
            'campaigns'   => $campaigns,
        ];
    }
}
