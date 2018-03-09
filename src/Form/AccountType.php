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
//            ->add('type', AccountTypeType::class)
            ->add('type', null, [
                'required' => true,
//                'data' => $type,
                'placeholder' => 'Select a type...',
//                'class' => \App\Entity\AccountType::class
            ])
            ->add('submit', ButtonType::class)
        ;

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
//        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    protected function addElements(FormInterface $form, \App\Entity\AccountType $type = null) {
        $form->add('type', EntityType::class, array(
            'required' => true,
            'data' => $type,
            'placeholder' => 'Select a type...',
            'class' => \App\Entity\AccountType::class
        ));
    }

    function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $data = $event->getData();

        // Search for selected type and convert it into an Entity
        $type = $this->em->getRepository(\App\Entity\AccountType::class)->find($data['type']);

        $this->addElements($form, $type);
    }

    function onPreSetData(FormEvent $event) {
        $account = $event->getData();

//        dump($event); exit;
        $form = $event->getForm();

        // When you create a new account, the type is always empty
        if (!empty($account)) {
            $type = $account->getType() ? $account->getType() :  null;
        }

        $this->addElements($form, $type ?? null);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
