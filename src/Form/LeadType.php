<?php

namespace App\Form;

use App\Entity\Lead;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class LeadType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('nip')
            ->add('phone')
            ->add('taskDescription')
            ->add('additionalInfo')
            ->add('userClientId',EntityType::class,[
                'class' => User::class,
                'required' => false,
                'placeholder' => 'Przypisz konto klienta do leada',
                'query_builder' => function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('u')
                        ->setParameter('role', '[]')
                        ->setParameter('company',$this->security->getUser()->getCompany())
                        ->where('u.roles = :role')
                        ->andWhere('u.company = :company');
                },
                'choice_label' => 'email',

            ])
            ->add('Zapisz',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lead::class,
        ]);
    }
}
