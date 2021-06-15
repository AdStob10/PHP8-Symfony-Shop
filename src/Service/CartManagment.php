<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class CartManagment
{

 
    public function __construct(private SessionInterface $session, private OrderRepository $orderRepository, private EntityManagerInterface $em, private Security $security)
    {}

    public function getCart(): Order
    {

        $cart = $this->orderRepository->findOneBy(
            ['id' => $this->getCartSession(),
            'status' => 0]
        );

        
        if(!$cart)
        {
            /** @var \App\Entity\User $user */
            $user = $this->security->getUser();
            if($user)
            {
                $cart = $this->orderRepository->findOneBy(
                    ['user' => $user,
                     'status' => 0]
                );
            }
        }

        return $cart ? $cart : new Order();
    }

    public function saveCart(Order $cart): void
    {

        $this->em->persist($cart);

        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        if($user)
        {
            $user->addOrder($cart);
            $this->em->persist($user);
        }
        $this->em->flush();

        $this->assignCartSession($cart);
    }

    public function addItemToCart(OrderProduct $item, Product $product): void
    {
        $cart = $this->getCart();
        
        // Assigne product details - product can change or be deleted
        $item->setProductName($product->getName());
        $item->setPriceProduct($product->getPrice());
        $item->setProduct($product);

        // Add to cart
        $cart->addItem($item);
        $this->saveCart($cart);
    }



    public function assignCartSession(Order $cart)
    {
        $this->session->set("cart", $cart->getId());
    }

    public function getCartSession(): ?int
    {
        return $this->session->get("cart");
    }

    public function removeCart()
    {
        $this->session->remove("cart");
    } 
}