<?php

namespace App\Form;

use App\Entity\Survey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name')
            ->add('Questions', CollectionType::class, [
                'entry_type' => QuestionType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'item'
                    ]
                ],
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => false,
                'by_reference' => true,
                'delete_empty' => true,
                'attr' => array(
                    'class' => 'table QuestionCollection',
                ),
            ])
            ->add('Save', SubmitType::class, [
                'label' => 'Opslaan',
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'SurveyType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Survey::class,
        ]);
    }
}
