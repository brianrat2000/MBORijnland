<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', null, [
                'label' => 'Naam',
            ])
            ->add('Type', null, [
                'label' => 'Type',
            ])
            ->add('Survey')
            ->add('QuestionOptions', CollectionType::class, [
                'entry_type' => QuestionOptionType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'item'
                    ]
                ],
                'label_attr' => ['class' => 'd-none'],
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'required'     => false,
                'by_reference' => true,
                'delete_empty' => true,
                'attr' => array(
                    'class' => 'QuestionOptionCollection',
                ),
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'QuestionType';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
