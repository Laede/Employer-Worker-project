<?php

namespace App\Form;

use App\Entity\Skills;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add('email')
            ->add('role',ChoiceType::class, [
                'choices' => [
                    'Employer'  => 'ROLE_EMPLOYER',
                    'Worker'    => 'ROLE_WORKER',
                ],
                'expanded' => true,
            ])
            ->add('skills', ChoiceType::class, [
                'class' => Skills::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
