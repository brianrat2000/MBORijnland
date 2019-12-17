<?php

namespace App\Form;

use App\Entity\QuestionOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\QuestionRepository;
use App\Repository\QuestionTypeRepository;

class QuestionOptionType extends AbstractType
{
    private $questionRepo;
    private $questionTypeRepo;

    public function __construct (QuestionRepository $questionRepository, QuestionTypeRepository $questionTypeRepository)
    {
        $this->questionRepo = $questionRepository;
        $this->questionTypeRepo = $questionTypeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Value')
            ->add('Question', null, [
                'choices' => $this->questionRepo->findBy(['Type' => $this->questionTypeRepo->findBy(['Name' => 'Multiple Choice'])])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionOption::class,
        ]);
    }
}
