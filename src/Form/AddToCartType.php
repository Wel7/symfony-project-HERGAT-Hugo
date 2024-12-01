<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantity',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'width: 80px;',
                ],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Range([
                        'min' => 1,
                        'max' => $options['productStock'],
                    ]),
                ],
                'data' => 1,
            ])
            ->add('productPrice', HiddenType::class, [
                'data' => $options['productPrice'],
            ])
            ->add('productId', HiddenType::class, [
                'data' => $options['productId'],
            ])->add('add', SubmitType::class, [
                'label' => 'Add to cart',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'productId' => null,
            'productStock' => null,
            'productPrice' => null,
        ]);

        $resolver->setRequired(['productId', 'productStock', 'productPrice']);
        $resolver->setAllowedTypes('productId', ['int', 'string']);
        $resolver->setAllowedTypes('productStock', 'int');
        $resolver->setAllowedTypes('productPrice', ['int', 'float']);
    }
}
