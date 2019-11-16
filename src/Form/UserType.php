<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;



class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Votre email'
            ))

            ->add('userName', TextType::class, array(
                'label' => 'Nom d\'utilisateur'
            ))

            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'help' => 'Votre mot de passse sera stocké de manière sécurisée',
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation du mot de passe'),
            ))

            ->add('birthdayDate', BirthdayType::class, [
                'placeholder' => [
                    'day' => 'Jour', 'month' => 'Mois', 'year' => 'Année'
                ],
                'format' => 'dd-MM-yyyy',
                'label' => 'Votre date de naissance',
                'required' => false,
            ])

            ->add('userStatut', ChoiceType::class, array(
                'choices' => array(
                    'Vous êtes un particulier' => 'particulier',
                    'Vous êtes un professionnel' => 'professionnel',
                ),
                'label' => 'Votre statut'
            ))

            ->add('termsAccepted', CheckboxType::class, array(
                'mapped' => false,
                'constraints' => new IsTrue(),
                'label' => 'Vous acceptez les conditions d\'utilisations'
            ))

            ->add('image', FileType::class, [
                'label' => 'Ajouter une photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/WebP',
                            'image/tif',
                            'image/png',
                            'image/gif',
                            'image/bmp',
                            'image/svg',
                        ],
                        'mimeTypesMessage' => 'Merci de télécharger une image au format jpeg, jpg, webp, tif, png, gif, bmp ou svg',
                    ])
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'button btn-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
