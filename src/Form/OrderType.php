<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
<<<<<<< HEAD

=======
use Symfony\Component\Validator\Constraints\IsTrue;
>>>>>>> 01335d30e4225194afac7e90a05acd82adf2b493

// Form for creating a new order
class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientName', TextType::class, [
                'label' => 'Name'
            ])
            ->add('clientCity',TextType::class, [
                'label' => 'City'
            ])
            ->add('clientStreet',TextType::class, [
                'label' => 'Street'
            ])
            ->add('clientCountry',CountryType::class, [
                'label' => 'Country'
            ])
            ->add('clientPostcode',TextType::class, [
                'label' => 'Postcode',
                'error_bubbling' => true
            ])
            ->add('acceptPolicy', CheckboxType::class,[
                'mapped' => False,
                'required' => True,
                'constraints' => [new IsTrue(['message' => 'You need to accept our policy!'])]
            ])
            ->add('paymentMethod',ChoiceType::class,[
                'choices' => array_keys(Order::paymentMethods),
                'choice_label' => function($choice, $key, $value) {
                    return Order::paymentMethods[$value];
                }
            ])
            ->add('paymentMethod',ChoiceType::class,[
                'choices' => array_keys(Order::paymentMethods),
                'choice_label' => function($choice, $key, $value) {
                    return Order::paymentMethods[$value];
                }
            ])
            ->add('create', SubmitType::class,[
                'label' => 'Make an order'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'validation_groups' => ['orderData']
        ]);
    }
}
