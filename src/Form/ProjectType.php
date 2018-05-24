<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Skills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5' => false,
                'data' => new \DateTime()
                ]
            )
            ->add('crewCount')
            ->add('budget')
            ->add('registerDeadline', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ],
                'html5' => false,
                'data' => new \DateTime("+5 day")])
            ->add('skills', EntityType::class, [
                'class' => Skills::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }

}
