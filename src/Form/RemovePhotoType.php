<?php

namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RemovePhotoType extends AbstractType
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
            ->add('remove', SubmitType::class, ['label' => 'Supprimer le photo', 'attr' => ['onclick' => 'confirm("Êtes-vous sûr de vouloir supprimer?")']])
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
