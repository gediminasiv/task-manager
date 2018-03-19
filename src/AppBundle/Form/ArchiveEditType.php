<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;

class ArchiveEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', TextType::class, [
            'label' => 'description'
        ]);

        $builder->add('arrivalTime', DateTimeType::class, [
            'label' => 'arrival_time',
            'format' => 'yyyy-MM-dd',
            'html5' => false,
            'attr' => [
                'class' => 'datepicker',
                'disabled' => 'disabled'
            ],
            'date_widget' => 'single_text',
            'minutes' => ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55']
        ]);

        $builder->add('departureTime', DateTimeType::class, [
            'label' => 'departure_time',
            'format' => 'yyyy-MM-dd',
            'html5' => false,
            'attr' => [
                'class' => 'datepicker',
                'disabled' => 'disabled'
            ],
            'date_widget' => 'single_text',
            'minutes' => ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55']
        ]);

        $builder->add('cancelledComment', TextareaType::class, [
            'label' => 'cancelled_comment',
            'required' => false
        ]);

        $builder->add('status', ChoiceType::class, [
            'label' => 'status',
            'multiple' => false,
            'expanded' => false,
            'choices' => [
                'open' => 0,
                'arrival' => 1,
                'delayed' => 2,
                'finished' => 3
            ]
        ]);

        $builder->add('user', EntityType::class, [
            'label' => 'responsible_user',
            'class' => User::class,
            'choice_label' => function ($user) {
                return $user->getFirstName()." ".$user->getLastName();
            }
        ]);

        $builder->add('project', EntityType::class, [
            'label' => 'project',
            'class' => Project::class,
            'choice_label' =>  function($project, $key, $index) {
                return $project->getTitle();
            },
        ]);

        $builder->add('created', DateTimeType::class, [
            'label' => 'created',
            'format' => 'yyyy-MM-dd',
            'html5' => false,
            'attr' => [
                'class' => 'datepicker',
                'disabled' => 'disabled'
            ],
            'date_widget' => 'single_text',
            'minutes' => ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55']
        ]);

        $builder->add('distance', TextType::class, [
            'label' => 'distance'
        ]);

        $builder->add('invoiceNo', TextType::class, [
            'label' => 'invoice_no',
            'required' => false
        ]);

        $builder->add('archived', ChoiceType::class, [
            'label' => 'archived',
            'multiple' => false,
            'expanded' => false,
            'choices' => [
                'yes' => true,
                'no' => false
            ]
        ]);

        $builder->add('finishedComment', TextType::class, [
            'label' => 'invoice_no',
            'required' => false
        ]);

        $builder->add('invoiceNo', TextType::class, [
            'label' => 'invoice_no',
            'required' => false
        ]);


        $builder->add('imageFile', FileType::class, [
            'label' => 'photo',
            'required' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_task';
    }


}
