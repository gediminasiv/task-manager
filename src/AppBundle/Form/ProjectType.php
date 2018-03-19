<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Client;

class ProjectType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->em->getRepository(Client::class)->findAll();

        $builder->add('title', TextType::class, [
            'label' => 'title'
        ])
        ->add('address', TextType::class, [
            'label' => 'address'
        ])
        ->add('telephone', TextType::class, [
            'label' => 'telephone'
        ])
        ->add('email', TextType::class, [
            'label' => 'email'
        ])
        ->add('client', EntityType::class, [
            'class' => Client::class,
            'label' => 'client',
            'empty_data' => null,
            'placeholder' => '-',
            'required' => false,
            'choice_label' =>  function($client, $key, $index) {
                return $client->getName();
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Project'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_project';
    }


}
