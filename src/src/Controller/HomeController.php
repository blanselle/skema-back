<?php

declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_COORDINATOR')]
#[Route('/admin')]
class HomeController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
