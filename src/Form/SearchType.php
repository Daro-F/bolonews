<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType as InputSearchType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', InputSearchType::class, [
            'label'=> false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Rechercher un article...'
            ]
        ]);
    }
}
