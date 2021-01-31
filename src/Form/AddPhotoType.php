<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;

class AddPhotoType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface    $builder
     * @param array                   $options
     *
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre de votre photo'])
            ->add('photo', FileType::class, [
                    'label' => 'Votre photo',
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5000k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger un type d\'image valide (ex. JPG ou PNG)',
                        ])
                    ]
            ])
            ->add('upload', SubmitType::class, ['label' => 'Ajouter photo'])
        ;
    }

    /**
     * Configure Options
     *
     * @param OptionsResolver    $resolver
     *
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
