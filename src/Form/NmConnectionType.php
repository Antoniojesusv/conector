<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NmConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', TextType::class)
            ->add('password', PasswordType::class)
            ->add('address', TextType::class)
            ->add('database', TextType::class)
            ->add('exposedPort', NumberType::class)
            ->add('authentication', HiddenType::class, [
                'mapped' => false,
                'data' => $options['type']
            ])
            ->add('edit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NmConnectionModel::class,
            'type' => '',
        ]);

        $resolver->setAllowedTypes('type', 'string');
    }
}
