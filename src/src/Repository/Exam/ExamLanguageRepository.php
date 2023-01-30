<?php

namespace App\Repository\Exam;

use App\Entity\Exam\ExamLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamLanguage[]    findAll()
 * @method ExamLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamLanguage::class);
    }

    public function findByProgramChannelIds(mixed $programChannelIds = null, array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder('exam_language');

        if (null !== $programChannelIds) {
            if (!is_array($programChannelIds)) {
                $programChannelIds = [$programChannelIds];
            }

            $qb
                ->join('exam_language.programChannels', 'program_channels')
                ->andWhere($qb->expr()->in('program_channels.id', ':_ids'))
                ->setParameter('_ids', $programChannelIds);
        }

        foreach($orderBy as $key => $value) {
            $qb->addOrderBy(sprintf('exam_language.%s', $key), $value);
        }

        return $qb->getQuery()->getResult();
    }
}