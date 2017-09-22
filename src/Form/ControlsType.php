<?php

/*
 * This file is part of episk core
 *
 * (c) Justin Leapline <justin@episki.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Controls;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\OwnersInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate  s.
 *
 * @author Justin Leapline <justin@episki.org>
 */
class ControlsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

        $builder
            ->add('number', null, [
                'label' => 'label.number',
            ])
            ->add('requirement', TextareaType::class, [
                'label' => 'label.requirement',
            ])
            ->add('description', TextareaType::class, [
	            'attr' => ['rows' => 10],
                'label' => 'label.description',
                'required' => false,
            ])
            ->add('owners', OwnersInputType::class, [
                'label' => 'label.owners',
                'required' => false,
            ])
            ->add('authorityref', EntityType::class, array(
			    'class' => 'App:Authority',
			    'label' => 'label.authorityref',
                'choice_name' => null,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false
		    ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Controls::class,
        ]);
    }
}

