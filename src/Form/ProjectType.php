<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Skills;
use App\Entity\User;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate')
            ->add('crewCount')
            ->add('budget')
            ->add('registerDeadline')
//            ->add('user', EntityType::class, array(
//                'class' => User::class,
//                'choice_label' => 'email'
//            ))
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
