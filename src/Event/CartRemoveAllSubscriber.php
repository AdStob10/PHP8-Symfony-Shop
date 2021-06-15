<?php

namespace App\Event;

use App\Entity\Order;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
USE Symfony\Component\Form\FormEvents;


class CartRemoveAllSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => "postSubmit"];
    }

    public function postSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if(!$cart instanceof Order)
            return;

        if($form->get('clear')->isClicked())
            $cart->removeAllItems();

    }


}