<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    // Statuses. In PHP 8.1 we can use enums
    const orderStatuses = ['CART','ORDERED','PROCESSING','FINISHED','CANCELED'];

    const orderManageStatuses = [1 => 'ORDERED', 2 => 'PROCESSING', 3 => 'FINISHED', 4 => 'CANCELED'];

    /**
     * @ORM\Column(type="integer")
     */
    private $status = 0;

    /**
     * @ORM\PreUpdate
     */
    public function statusUpdate(PreUpdateEventArgs $event)
    {
        if($event->hasChangedField('status'))
        {
            $this->statusDate = new \DateTime();
        }
    }

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="orderObj", cascade={"persist","remove"} ,orphanRemoval=true)
     */
    private $items;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $statusDate;

    /**
     * @ORM\Column(type="string", length=150, nullable = true)
     */
    #[Assert\NotBlank(groups:['orderData'])]
    #[Assert\Length(min:10, groups:['orderData'])]
    #[Assert\Regex(pattern:"/^[a-zA-Z]+\w*/", groups:['orderData'])]
    private $clientName;

    /**
     * @ORM\Column(type="string", length=200, nullable = true)
     */
    #[Assert\NotBlank(groups:['orderData'])]
    #[Assert\Length(min:3, groups:['orderData'])]
    private $clientCity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    #[Assert\NotBlank(groups:['orderData'])]
    #[Assert\Length(min:10, groups:['orderData'])]
    #[Assert\Regex(pattern:"/^\w+/", groups:['orderData'])]
    private $clientStreet;

    /**
     * @ORM\Column(type="string", length=9, nullable=true)
     */
    #[Assert\NotBlank(groups:['orderData'])]
    #[Assert\Length(min:6, 
        max:9, 
        minMessage:"Zip code must have at least 6 characters",
        maxMessage:"Zip code must have maximum 9 characters",
        groups:['orderData']
    )]
    private $clientPostCode;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    #[Assert\NotBlank(groups:['orderData'])]
    #[Assert\Length(min:2, groups:['orderData'])]
    private $clientCountry;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderProduct $item): self
    {

        foreach($this->getItems() as $arrItem)
        {
            if($arrItem->equals($item))
            {
                $arrItem->setQuantity($arrItem->getQuantity() + $item->getQuantity());
                return $this;
            }
        }


        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setOrderObj($this);
        }
        return $this;
    }

    public function removeItem(OrderProduct $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrderObj() === $this) {
                $item->setOrderObj(null);
            }
        }

        return $this;
    }

    public function removeAllItems(): self
    {
        foreach($this->getItems() as $arrItem)
        {
            $this->removeItem($arrItem);
        }

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getStatusDate(): ?\DateTimeInterface
    {
        return $this->statusDate;
    }

    public function setStatusDate(?\DateTimeInterface $statusDate): self
    {
        $this->statusDate = $statusDate;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getClientCity(): ?string
    {
        return $this->clientCity;
    }

    public function setClientCity(string $clientCity): self
    {
        $this->clientCity = $clientCity;

        return $this;
    }


    public function getOrderSum(): ?float
    {
        $sum = 0.0;

        foreach($this->getItems() as $arrItem)
        {
            $sum += $arrItem->getSum();
        }

        return $sum;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClientStreet(): ?string
    {
        return $this->clientStreet;
    }

    public function setClientStreet(?string $clientStreet): self
    {
        $this->clientStreet = $clientStreet;

        return $this;
    }

    public function getClientPostCode(): ?string
    {
        return $this->clientPostCode;
    }

    public function setClientPostCode(?string $clientPostCode): self
    {
        $this->clientPostCode = $clientPostCode;

        return $this;
    }

    public function getClientCountry(): ?string
    {
        return $this->clientCountry;
    }

    public function setClientCountry(?string $clientCountry): self
    {
        $this->clientCountry = $clientCountry;

        return $this;
    }
}
