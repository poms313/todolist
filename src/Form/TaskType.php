<?php

namespace App\Form;

use App\Entity\UserTask;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taskName', TextType::class, array(
                'label' => 'Nom de la tâche'
            ))

            ->add('taskDescription', TextType::class, array(
                'label' => 'Description'
            ))

            ->add('taskCategory', ChoiceType::class, array(
                'choices' => array(
                    'Travail' => 'Travail',
                    'Loisir' => 'Loisir',
                    'Personnel' => 'Personnel',
                    'Obligation' => 'Obligation',
                ),
                'label' => 'Catégorie'
            ))

            ->add('taskStartDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Début de la tâche',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])


            ->add('taskEndDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'label' => 'Fin de la tâche',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])

            ->add('taskNumberMaxEmail', ChoiceType::class, array(
                    'choices' => array(
                        '1' => 1,
                        '2' => 2,
                        '3' => 3,
                        '4' => 4,
                        '5' => 5,
                        '10' => 10,
                        '20' => 20,
                        '50' => 50,
                        '100' => 100,
                    ),
                    'label' => 'Nombre de mails de rappel'
            ))

            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'button btn-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserTask::class,
        ]);
    }
}
