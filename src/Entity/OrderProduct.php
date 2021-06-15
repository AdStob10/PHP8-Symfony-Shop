<?php

namespace App\Entity;

use App\Repository\OrderProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderProductRepository::class)
 */
class OrderProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $Product;

    /**
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private $Quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderObj;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $productName;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $priceProduct;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    public function setProduct(?Product $Product): self
    {
        $this->Product = $Product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): self
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getOrderObj(): ?Order
    {
        return $this->orderObj;
    }

    public function setOrderObj(?Order $orderObj): self
    {
        $this->orderObj = $orderObj;

        return $this;
    }


    public function equals(OrderProduct $obj)
    {
        return $this->getProduct()->getId() === $obj->getProduct()->getId();
    }

    public function getSum(): ?float
    {
        if($this->Quantity <= 0 )
            return $this->getPriceProduct();

        return $this->getQuantity() * $this->getPriceProduct();
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getPriceProduct(): ?string
    {
        return $this->priceProduct;
    }

    public function setPriceProduct(string $priceProduct): self
    {
        $this->priceProduct = $priceProduct;

        return $this;
    }
}
