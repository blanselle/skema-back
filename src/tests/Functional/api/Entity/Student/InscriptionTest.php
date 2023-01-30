<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity\Student;

use App\Message\NotificationMessage;
use App\Tests\Functional\api\AbstractApiTest;
use Symfony\Component\Messenger\Transport\InMemoryTransport;

class InscriptionTest extends AbstractApiTest
{
    private const EMAIL = 'inscriptiontest@skema.fr';
    private const PASSWORD = 'Azertyuiop123!';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateAst1Student(): void
    {
        $email = 'student.' . self::EMAIL;
        $response = $this->createAst1CheckDiploma(
            email: $email,
            password: self::PASSWORD,
        );

        $this->assertSame(
            "<p>Vous allez recevoir un e-mail afin d’activer votre compte. <br />Merci de cliquer sur le lien d’activation afin de pouvoir accéder à votre espace.</p>",
            json_decode($response->getContent())->message,
        );

        $user = $this->getUser($email);

        $this->assertSame('start', $user->getStudent()->getState());
    }

    public function testCreateAst1CheckDiploma(): void
    {
        $email = 'check_diploma.' . self::EMAIL;
        $this->createAst1CheckDiploma(
            email: $email,
            password: self::PASSWORD,
        );

        $response = $this->activeAccount();

        $this->assertSame(
            "Bienvenue sur le site Concours AST SKEMA.<br /> Vous pouvez dès à présent vous connectez à votre espace et finaliser votre candidature au plus vite.<br />ATTENTION votre inscription sera effective dès lors que vous procéderez au paiement des frais d’inscription ou téléchargement de l’attestation de bourse ainsi que des pièces administratives obligatoires!",
            json_decode($response->getContent())->message,
        );

        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async_notifier');
        $this->assertNotEmpty($transport->getSent());
        /** @var NotificationMessage $notificationMessage */
        $notificationMessage = $transport->getSent()[0]->getMessage();

        $this->assertSame($notificationMessage->getSubject(), "Votre candidature est en attente");
        $this->assertNull($notificationMessage->getParentId());
        $this->assertNull($notificationMessage->getSenderId());
        
        $data = $this->getMe($this->getToken($email, self::PASSWORD));
        
        $this->assertSame('check_diploma', $this->getMe($this->getToken($email, self::PASSWORD))['student']['state']);
        $this->assertSame($notificationMessage->getContent(), "Bonjour {$data['firstName']}, l’éligibilité de votre candidature va être contrôlée. Vous pouvez compléter votre dossier administratif. Le service concours");

        $this->assertSame($notificationMessage->getReceiverId()->__toString(), $data['id']);
    }
    
    public function testCreateAst1Created(): void
    {
        $email = 'created.' . self::EMAIL;
        $this->createAst1Created(
            email: $email,
            password: self::PASSWORD,
        );

        $response = $this->activeAccount();

        $this->assertSame(
            "Bienvenue sur le site Concours AST SKEMA.<br /> Vous pouvez dès à présent vous connectez à votre espace et finaliser votre candidature au plus vite.<br />ATTENTION votre inscription sera effective dès lors que vous procéderez au paiement des frais d’inscription ou téléchargement de l’attestation de bourse ainsi que des pièces administratives obligatoires!",
            json_decode($response->getContent())->message,
        );

        $this->assertSame('created', $this->getMe($this->getToken($email, self::PASSWORD))['student']['state']);
    }

    public function testCreateAst1Exemption(): void
    {
        $email = 'exemption.' . self::EMAIL;
        $this->createAst1Exemption(
            email: $email,
            password: self::PASSWORD,
        );

        $response = $this->activeAccount();

        $this->assertSame(
            "Bonjour, <br /><br /> Vous souhaitez participer au concours AST SKEMA. <br />Votre profil nécessite une dérogation. Merci de contacter le service concours.",
            json_decode($response->getContent())->message,
        );
    }

    public function testCreateAst1ExemptionAndCheckDiploma(): void
    {
        $email = 'exemption_check_diploma.' . self::EMAIL;
        $this->createAst1Exemption(
            email: $email,
            password: self::PASSWORD,
        );

        $response = $this->activeAccount();

        $this->assertSame(
            "Bonjour, <br /><br /> Vous souhaitez participer au concours AST SKEMA. <br />Votre profil nécessite une dérogation. Merci de contacter le service concours.",
            json_decode($response->getContent())->message,
        );
    }
}