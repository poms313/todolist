<?php
// src/Form/ContactType.php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Votre nom'
            ))

            ->add('from', EmailType::class, array(
                'label' => 'Votre email'
            ))

            ->add('message', TextareaType::class, array(
                'label' => 'Votre message'
            ))

            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'button btn-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            //'data_class' => Contact::class,
        ]);
    }
}
