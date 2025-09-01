<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Email;
use App\Domain\Entity\EmailEvent;
use App\Domain\Enum\EmailEventType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Email>
 */
class EmailRepository extends ServiceEntityRepository implements EmailRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }


    public function add(Email $e): void
    {
        $em = $this->getEntityManager();
        $em->persist($e);
        $em->flush();
    }
    public function findOneByTrackingId(string $tid): ?Email
    {
        return $this->findOneBy(['trackingId' => $tid]);
    }

    public function getCampaignStats(): array
    {
        $qb = $this->createQueryBuilder('e')
        ->select('e.campaignId AS campaignId')
        ->addSelect('COUNT(e.id) AS sent')
        ->addSelect('SUM(CASE WHEN ev.type = :open THEN 1 ELSE 0 END) AS opens')
        ->addSelect('SUM(CASE WHEN ev.type = :click THEN 1 ELSE 0 END) AS clicks')
        ->leftJoin(EmailEvent::class, 'ev', 'WITH', 'ev.trackingId = e.trackingId')
        ->groupBy('e.campaignId')
        ->setParameter('open', EmailEventType::OPEN->value)
        ->setParameter('click', EmailEventType::CLICK->value);

        return $qb->getQuery()->getArrayResult();



    }







}
