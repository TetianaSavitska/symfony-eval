<?php

namespace AppBundle\Form;

use AppBundle\Entity\Science;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('science', EntityType::class, [
                'class' => Science::class
            ])
            ->add('author')
            ->add('description')
            ->add('content');

            if ($options['admin']){
                $builder->add('publishedAt');
            }

            if ($options['validated_field']){
                $builder->add('validated');
            }

            $builder
                ->add('save', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-sm btn-primary',
                    ]
            ]);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Publication',
                'validated_field' => true,
                'admin' => true,
            ))
            ->setAllowedTypes('validated_field', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_publication';
    }


}
