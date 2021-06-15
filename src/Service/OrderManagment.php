<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;


class OrderManagment 
{

    public function __construct(private EntityManagerInterface $em, private CartManagment $cartManagment)
    {}

    public function createEmptyOrder(): ?Order
    {
        $order = new Order();
        $order->setStatus(0);
        return $order;
    }

    public function makeOrder(Order $order):Order
    {
        $order->setCreatedDate(new \DateTime());
        $this->setOrderStatus($order, 1);
        $this->em->persist($order);
        $this->em->flush();

        $this->cartManagment->removeCart();

        return $order;
    }

    public function setOrderStatus(Order $order, int $status)
    {
        $order->setStatusDate(new \DateTime());
        $order->setStatus($status);
    }

}