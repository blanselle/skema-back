<?php

declare(strict_types=1);

namespace App\Controller\Admissibility;

use App\Constants\Admissibility\Bonus\BonusListConstants;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\CategoryRepository;
use App\Service\Admissibility\GetAllBonuses;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Knp\Component\Pager\PaginatorInterface;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/admissibility/bonus')]
#[IsGranted('ROLE_RESPONSABLE')]
class BonusController extends AbstractController
{
    #[Route('', name: 'admissibility_bonus_index', methods: ['GET'])]
    public function index(
        GetAllBonuses $getAllBonuses,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        $categoryId = $request->query->get('categoryId', (string)$categories[0]->getId());

        $pagination = $paginator->paginate(
            $getAllBonuses->get(
                criteria: ['category' => (int)$categoryId],
                sort: $request->query->get('sort', 'b.value'),
                direction: $request->query->get('direction', 'desc')
            ),
            $request->query->getInt('page', 1),
            50,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'b.value',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'desc',
            ]
        );

        $pagination->setCustomParameters(['align' => 'right',]);

        return $this->render('admissibility/bonus/index.html.twig', [
            'categories' => $categories,
            'pagination' => $pagination,
            'categoryId' => (int)$categoryId,
        ]);
    }

    #[Route('/{type}/{id}/edit', name: 'admissibility_bonus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BonusInterface $bonus, EntityManagerInterface $em): Response
    {
        $form = $this->getForm($bonus);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admissibility_bonus_index', ['categoryId' => $bonus->getCategory()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admissibility/bonus/edit.html.twig', [
            'bonus' => $bonus,
            'form' => $form,
            'category' => $bonus->getCategory(),
        ]);
    }

    #[Route('/{type}/new', name: 'admissibility_bonus_new', methods: ['GET', 'POST'])]
    public function new(string $type, Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        try{
            $class = constant(BonusListConstants::class.'::'.strtoupper($type));
        } catch(Error $e) {
            throw new BadRequestException('Type is not valid');
        }

        /** @var BonusInterface $bonus */
        $bonus = new $class();

        $category = $categoryRepository->findOneBy(['key' => $type], []);
        $bonus->setCategory($category);

        $form = $this->getForm($bonus);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bonus);
            $em->flush();

            return $this->redirectToRoute('admissibility_bonus_index', ['categoryId' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admissibility/bonus/new.html.twig', [
            'bonus' => $bonus,
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/{type}/{id}', name: 'admissibility_bonus_delete', methods: ['POST'])]
    public function delete(Request $request, BonusInterface $bonus, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bonus->getId(), strval($request->request->get('_token')))) {
            $em->remove($bonus);
            $em->flush();
        }

        return $this->redirectToRoute('admissibility_bonus_index', [], Response::HTTP_SEE_OTHER);
    }

    private function getForm(BonusInterface $bonus): FormInterface
    {
        return $this->createForm('App\Form\Admissibility\Bonus\\' . (new ReflectionClass($bonus::class))->getShortName() . 'Type', $bonus);
    }
}
