<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\User;
use AppBundle\Entity\Project;
use AppBundle\Entity\Client;

class EntitySelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', EntityType::class, [
            'label' => 'user',
            'class' => User::class,
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $builder->add('client', EntityType::class, [
            'label' => 'client',
            'class' => Client::class,
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $builder->add('project', EntityType::class, [
            'label' => 'project',
            'class' => Project::class,
            'attr' => [
                'class' => 'form-control'
            ]
        ]);
    }
}
