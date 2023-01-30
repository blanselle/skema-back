<?php

declare(strict_types=1);

namespace App\Repository\Notification;

use App\Entity\Notification\NotificationTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationTemplate[]    findAll()
 * @method NotificationTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationTemplate::class);
    }
}
