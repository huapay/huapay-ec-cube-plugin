<?php

/*
 * This file is part of the HuapayPlugin
 *
 * Copyright (C) 2018 Huapay
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\HuapayPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class HuapayPluginConfigType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('api_token', 'text', array(
                'label' => 'APIトークン',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('is_testing', 'choice', array(
                'choices' => array(
                    0 => '商用環境',
                    1 => 'テスト環境'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'empty_value' => false,
            ))
	    ->add('uses_unionpay', 'choice', array(
                'choices' => array(
                    1 => '有効',
                    0 => '無効',
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'empty_value' => false,
		'mapped' => false,
            ))
            ->add('uses_alipay', 'choice', array(
                'choices' => array(
                    1 => '有効',
                    0 => '無効',
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'empty_value' => false,
		'mapped' => false,
            ))
            ->add('uses_wechatpay', 'choice', array(
                'choices' => array(
                    1 => '有効',
                    0 => '無効',
                ),
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'empty_value' => false,
		'mapped' => false,
            ));
    }

    public function getName()
    {
        return 'huapayplugin_config';
    }

}
