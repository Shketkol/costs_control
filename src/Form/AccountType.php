<?php

namespace App\Form;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', null, [
                'required' => true,
                'placeholder' => 'Select a type...',
            ])
            ->add('balance')
            ->add('submit', ButtonType::class)
        ;
    }

    protected function addElements(FormInterface $form, \App\Entity\AccountType $type = null) {
        $form->add('type', EntityType::class, array(
            'required' => true,
            'data' => $type,
            'placeholder' => 'Select a type...',
            'class' => \App\Entity\AccountType::class
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
