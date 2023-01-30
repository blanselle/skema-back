<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\LandingPage;

use App\Entity\Admissibility\LandingPage\AdmissibilityStudentToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdmissibilityStudentToken>
 *
 * @method AdmissibilityStudentToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdmissibilityStudentToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdmissibilityStudentToken[]    findAll()
 * @method AdmissibilityStudentToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdmissibilityStudentTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmissibilityStudentToken::class);
    }

    public function getAdmissibilityStudent(string $requestToken): array|false
    {
        $connection = $this->_em->getConnection();
        $query = $connection->prepare("
            SELECT s.identifier, s.state, u.last_name, u.first_name, s.program_channel_id
            FROM 
                admissibility_student_token ast
            INNER JOIN student s ON s.id = ast.student_id
            INNER JOIN users u ON u.student_id = ast.student_id 
            WHERE ast.token = :requestToken
		");
        $result = $query->executeQuery(['requestToken' => $requestToken]);
        return $result->fetchAssociative();
    }

    public function getAllTokens(): array
    {
        return $this->createQueryBuilder('t')->select('t.token')->getQuery()->getResult();
    }
}