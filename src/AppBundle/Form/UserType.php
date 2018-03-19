<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Client;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class, [
            'label' => 'email'
        ])
        ->add('password', PasswordType::class, [
            'label' => 'password',
        ])
        ->add('firstName', TextType::class, [
            'label' => 'first_name'
        ])->add('lastName', TextType::class, [
            'label' => 'last_name'
        ])->add('telephone', TextType::class, [
            'label' => 'telephone'
        ]);

        $builder->add('enabled', CheckboxType::class, [
            'label' => 'enabled',
            'required' => false
        ]);

        $builder->add('client', EntityType::class, [
            'label' => 'client',
            'class' => Client::class,
            'empty_data' => null,
            'placeholder' => '-',
            'required' => false,
        ]);

        $builder->add('roles', ChoiceType::class, [
            'label' => 'roles',
            'mapped' => false,
            'multiple' => true,
            'expanded' => false,
            'choices' => [
                'service_man' => 'ROLE_USER',
                'support' => 'ROLE_ADMIN',
                'admin' => 'ROLE_SUPER_ADMIN',
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
