<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\Entity\Order;

class RemoveFromCartSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents() : array
    {
        return [FormEvents::POST_SUBMIT => 'postSubmit'];
    }

    public function postSubmit(FormEvent $event):void
    {
        $form = $event->getForm();
        $cart = $form->getData();

        if(!$cart instanceof Order)
            return;
        
        foreach($form->get('items')->all() as $item)
        {
            if($item->get('action')->isClicked())
            {
                $cart->removeItem($item->getData());
                break;
            }
        }
    }


}


