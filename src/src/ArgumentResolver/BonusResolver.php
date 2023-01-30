<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Interface\BonusInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class BonusResolver implements ArgumentValueResolverInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (BonusInterface::class !== $argument->getType()) {
            return false;
        }

        $bonusType = $request->get('type');
        if(null === $bonusType) {
            throw new BadRequestException('Type of Bonus (type) missing in parameter');
        }

        return true;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @phpstan-ignore-next-line */
        $bonus = $this->em->getRepository(sprintf('App\Entity\Admissibility\Bonus\%s', $request->get('type')))->findOneById($request->get('id'));
        
        if(null === $bonus) {
            throw new EntityNotFoundException('The bonus does not exists');
        }

        yield $bonus;
    }
}
