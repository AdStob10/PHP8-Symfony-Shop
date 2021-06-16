<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Form - Delete category with possible product transfer
class DeleteCategoryType extends AbstractType
{

    public function __construct(private EntityManagerInterface $em)
    {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('categories',EntityType::class, [
            'mapped' => false,
            'class' => Category::class,
            'placeholder' => 'Choose category to transfer products',
            'query_builder' => $this->getQueryBuilder($options),
            'choice_label' => 'name',
            'required' => false
            ])
        ->add('delete',SubmitType::class);
    }


    public function getQueryBuilder( array $options)
    {
        
        return $this->em->createQueryBuilder()
                    ->select('c')
                    ->from('App:Category', 'c')
                    ->where('c.id NOT IN (?1)')
                    ->setParameter(1, $options['notId']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'notId' => 0
        ]);

        $resolver->setAllowedTypes('notId', 'int');
    }
}
