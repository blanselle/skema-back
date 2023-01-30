<?php

namespace App\Entity\Payment;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Action\Payment\PostOrderPaymentRequest;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Dto\OrderPaymentRequestInput;
use App\Entity\Exam\ExamSession;
use App\Entity\Loggable\History;
use App\Entity\Student;
use App\Repository\Payment\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'normalization_context' => [
                'groups' => ['order:collection:read'],
            ],
        ],
        'payment_request' => [
            'method' => 'POST',
            'path' => '/orders/payment/request',
            'status' => 301,
            'controller' => PostOrderPaymentRequest::class,
            'input' => OrderPaymentRequestInput::class,
            'read' => false,
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'openapi_context' => [
                'summary' => 'Request payment',
                'description' => 'Request a payment for fees',
                'responses' => [
                    '301' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'string',
                                    'example' => 'https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/XXXXX',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['order:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
        ],
    ],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['type' => 'exact', 'examSession' => 'exact'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    private ?int $amount = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    #[Gedmo\Versioned]
    private string $state = OrderWorkflowStateConstants::STATE_CREATED;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    private ?ExamSession $examSession = null;

    #[ORM\OneToMany(mappedBy: 'indent', targetEntity: Payment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups([
        'order:item:read',
        'order:collection:read',
    ])]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getExamSession(): ?ExamSession
    {
        return $this->examSession;
    }

    public function setExamSession(?ExamSession $examSession): self
    {
        $this->examSession = $examSession;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setIndent($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getIndent() === $this) {
                $payment->setIndent(null);
            }
        }

        return $this;
    }

    public function canRecreate(): bool
    {
        $can = true;
        foreach ($this->payments as $payment) {
            if (
                $payment->getState() === PaymentWorkflowStateConstants::STATE_CREATED or
                $payment->getState() === PaymentWorkflowStateConstants::STATE_IN_PROGRESS or
                $payment->getState() === PaymentWorkflowStateConstants::STATE_VALIDATED

            ) {
                $can = false;
                break;
            }
        }

        return $can;
    }
}
