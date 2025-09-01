<?php

namespace App\Domain\Repository;

use App\Domain\Entity\EmailEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailEvent>
 */
class EmailEventRepository extends ServiceEntityRepository implements EmailEventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailEvent::class);
    }

    public function log(EmailEvent $e): void
    {
        $em = $this->getEntityManager();
        $em->persist($e);
        $em->flush();
    }
    public function countByCampaignAndType(string $cid, string $type): int
    {
        return (int)$this->createQueryBuilder('e')->select('COUNT(e.id)')
          ->where('e.type = :t')->andWhere('e.meta = :cid')
          ->setParameter('t', $type)->setParameter('cid', $cid)
          ->getQuery()->getSingleScalarResult();
    }
    public function countRecipientsByCampaign(string $cid): int
    {
        $conn = $this->getEntityManager()->getConnection();
        return (int)$conn->fetchOne('SELECT COUNT(DISTINCT tracking_id) FROM email_event WHERE meta = :cid', ['cid' => $cid]);
    }
    public function countByType(string $type): int
    {

        return (int) $this->createQueryBuilder('ev')
        ->select('COUNT(DISTINCT ev.trackingId)')
        ->where('ev.type = :type')
        ->setParameter('type', $type)
        ->getQuery()
        ->getSingleScalarResult();
    }




}
