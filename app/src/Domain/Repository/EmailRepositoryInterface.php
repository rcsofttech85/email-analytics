<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Email;

interface EmailRepositoryInterface
{
    public function add(Email $email): void;
    public function findOneByTrackingId(string $trackingId): ?Email;
    public function count(array $criteria = []): int;
    public function getCampaignStats(): array;
}
