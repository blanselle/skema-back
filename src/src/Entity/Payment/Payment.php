<?php

namespace App\Entity\Payment;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\Payment\GetPaymentCapture;
use App\Action\Payment\GetPaymentStatus;
use App\Constants\Payment\PaymentExternalStatusConstants;
use App\Constants\Payment\PaymentModeConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Loggable\History;
use App\Entity\Traits\DateTrait;
use App\Repository\Payment\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'payments_capture' => [
            'method' => 'GET',
            'path' => '/payments/{id}/capture',
            'controller' => GetPaymentCapture::class,
            'condition' => "request.server.get('APP_ENV') != 'prod'",
            'normalization_context' => ['groups' => ['payment:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getIndent().getStudent().getUser() == user',
            'openapi_context' => [
                'summary' => 'Request payment capture',
                'description' => 'Capture payment after submitting PSP form',
            ],
        ],
        'payment_status' => [
            'method' => 'GET',
            'path' => '/payments/{id}/status',
            'controller' => GetPaymentStatus::class,
            'normalization_context' => ['groups' => ['payment:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getIndent().getStudent().getUser() == user',
            'openapi_context' => [
                'summary' => 'Retrieve payment status',
                'description' => 'Retrieve payment status',
            ],
        ],
    ],
)]
class Payment
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'payment:item:read',
    ])]
    private int $id;

    #[ORM\Column(length: 255, options: ['default' => 'online'])]
    #[Groups([
        'payment:item:read',
        'order:collection:read',
    ])]
    private string $mode = PaymentModeConstants::PAYMENT_MODE_ONLINE;

    #[ORM\Column(type: 'string', length: 255)]
    #[Gedmo\Versioned]
    #[Groups([
        'payment:item:read',
        'order:collection:read',
    ])]
    private string $state = PaymentWorkflowStateConstants::STATE_CREATED;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $redirectUrl = null; // the redirect url from FO

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $merchantReference;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'order:collection:read',
    ])]
    private ?string $externalPaymentID = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $externalReturnMAC = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $externalHostedCheckoutId = null;

    #[Assert\Choice(
        callback: [PaymentExternalStatusConstants::class, 'getConsts'],
        message: "Status {{ value }} invalide, veuillez renseigner l'un des status suivants : {{ choices }}"
    )]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'order:collection:read',
    ])]
    private ?string $externalStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $additionalInformation = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $indent = null;

    public function __construct()
    {
        $this->merchantReference = Uuid::v6();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getMerchantReference(): Uuid
    {
        return $this->merchantReference;
    }

    public function getExternalPaymentID(): ?string
    {
        return $this->externalPaymentID;
    }

    public function setExternalPaymentID(?string $externalPaymentID): self
    {
        $this->externalPaymentID = $externalPaymentID;

        return $this;
    }

    public function getExternalReturnMAC(): ?string
    {
        return $this->externalReturnMAC;
    }

    public function setExternalReturnMAC(?string $externalReturnMAC): self
    {
        $this->externalReturnMAC = $externalReturnMAC;

        return $this;
    }

    public function getExternalHostedCheckoutId(): ?string
    {
        return $this->externalHostedCheckoutId;
    }

    public function setExternalHostedCheckoutId(?string $externalHostedCheckoutId): self
    {
        $this->externalHostedCheckoutId = $externalHostedCheckoutId;

        return $this;
    }

    public function getExternalStatus(): ?string
    {
        return $this->externalStatus;
    }

    public function setExternalStatus(?string $externalStatus): self
    {
        $this->externalStatus = $externalStatus;

        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(?string $additionalInformation): self
    {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    public function getIndent(): ?Order
    {
        return $this->indent;
    }

    public function setIndent(?Order $indent): self
    {
        $this->indent = $indent;

        return $this;
    }
}
