<?php

namespace App\Form;

use App\Entity\Transfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Upload your file
            ->add('file', FileType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your file'],

              // unmapped means that this field is not associated to any entity property
              'mapped' => false,

              // make it optional so you don't have to re-upload the PDF file
              // everytime you edit the Transfer details
              'required' => false,

              // unmapped fields can't define their validation using annotations
              // in the associated entity, so you can use the PHP constraint classes
              'constraints' => [
                new File([
                  'maxSize' => '2048k',
                ])
              ],
            ])
            ->add('recipient', TextType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Send to'],
            ])
            ->add('sender', TextType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your email'],
            ])
            ->add('message', TextareaType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Message'],
            ])
            ->add('submit', SubmitType::class,[
              'label' => 'Transfer',
              'attr'=>['class'=>'save'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
        ]);
    }
}
