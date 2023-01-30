<?php

declare(strict_types=1);

namespace App\Controller\ResetPassword;

use App\Entity\User;
use App\Service\Mail\ResetPasswordMailDispatcher;
use App\Service\User\TokenManager;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[Route('/api/reset-password')]
class ResetPasswordController extends AbstractController
{
    private Utils $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    /**
     * @SuppressWarnings(PHPMD.EmptyCatchBlock)
     */
    #[Route('/request', name: 'reset_password_request', methods: ['POST'])]
    public function request(Request $request, ResetPasswordMailDispatcher $dispatcher): Response
    {
        $params = $request->toArray();

        if (!isset($params['email'])) {
            throw new BadRequestException('Email parameter is missing');
        }

        try {
            $dispatcher->dispatch($params['email']);
        } catch (UserNotFoundException $e) {
        } finally {
            return $this->json([
                'code' => 200,
                'message' => 'reset password request sent',
            ]);
        }
    }

    #[Route('/reset', name: 'reset_password', methods: ['POST'])]
    public function reset(
        Request $request,
        TokenManager $tokenManager,
        EntityManagerInterface $em,
    ): Response {
        $params = $request->toArray();
        if (!isset($params['token'])) {
            return $this->json([
                'message' => sprintf('Token missing'),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($params['password'])) {
            return $this->json([
                'message' => sprintf('Password missing'),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $tokenManager->parse($params['token']);
        } catch (JWTDecodeFailureException $e) {
            return $this->json([
                'message' => $this->utils->getMessageByKey('ERROR_REINIT_PASSWORD'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->setPlainPassword($params['password']);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => $this->utils->getMessageByKey('MSG_REINIT_PASSWORD_SUCCESS'),
        ]);
    }

    #[Route('/modify', name: 'change_password', methods: ['POST'])]
    #[IsGranted('ROLE_CANDIDATE')]
    public function change(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasher,
        TokenStorageInterface $tokenStorage
    ): Response {
        $params = $request->toArray();
        if ($params['confirmation_password'] !== $params['new_password']) {
            return $this->json([
                'message' => sprintf('Le mot de passe de confirmation ne correspond pas au nouveau'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $token = $tokenStorage->getToken();
        /**
         * @var User $user
         */
        $user = $token->getUser();

        if (null == $user || false === $userPasswordHasher->isPasswordValid($user, $params['old_password'])) {
            return $this->json([
                'message' => sprintf('L\'email ou le mot de passe ne correspond pas'),
            ], Response::HTTP_BAD_REQUEST);
        }
        $user->setPlainPassword($params['new_password']);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Votre mot de passe a bien été modifié',
        ]);
    }
}
