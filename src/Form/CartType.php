<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Order;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Event\RemoveFromCartSubscriber;
use App\Event\CartRemoveAllSubscriber;

// Cart form 
// Collection of cart items
class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class,[
                'entry_type' => CartItemType::class
            ])
            ->add('change', SubmitType::class)
            ->add('clear',SubmitType::class)
        ;

        $builder->addEventSubscriber(new RemoveFromCartSubscriber());
        $builder->addEventSubscriber(new CartRemoveAllSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
    }
}
