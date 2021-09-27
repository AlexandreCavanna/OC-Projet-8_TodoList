<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class TaskType extends AbstractType
{
    private Security $user;

    /**
     * TaskType constructor.
     */
    public function __construct(Security $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class)
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $user = $this->user->getUser();

            if ($user) {
                $event->getData()->setAuthor($user);
            }
        });
    }
}
