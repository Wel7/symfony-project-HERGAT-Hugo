<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Product;
use App\Entity\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use App\Enum\ProductStatus;
use App\Form\ImageType;

class ProductFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('price', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01',
                    'empty_data' => '0',
                    'min' => '0',
                ],
                'html5' => true,
            ])
            ->add('description', type: TextareaType::class)
            ->add('stock', IntegerType::class, [
                'attr' => [
                    'min' => 0, 
                ],
            ])
            ->add('status', EnumType::class, options: ['class' => ProductStatus::class])
            ->add('Categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('image', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__name__', 
                'required' => true,
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                if (empty($data['image'])) {
                    // Add an error to the 'image' field
                    $form = $event->getForm();
                    $form->addError(new FormError('At least one image must be added.'));
                }
                if (empty($data['Categories'])) {
                    // Add an error to the 'image' field
                    $form = $event->getForm();
                    $form->addError(new FormError('At least one category must be added.'));
                }
                if (isset($data['stock']) && $data['stock'] == 0) {
                    $data['status'] = ProductStatus::RUPTURE;
                    $event->setData($data);
                }
                $event->setData($data);
            })
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
