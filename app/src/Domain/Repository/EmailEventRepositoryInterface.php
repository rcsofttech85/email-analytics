<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EmailEvent;

interface EmailEventRepositoryInterface
{
    public function log(EmailEvent $event): void;
    public function countByCampaignAndType(string $campaignId, string $type): int;
    public function countRecipientsByCampaign(string $campaignId): int;
    public function countByType(string $type): int;
}
