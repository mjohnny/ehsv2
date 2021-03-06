<?php

namespace AppBundle\Form;


use AppBundle\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label_attr'=>array('hidden'=>'hidden'),
                'attr'=>array('placeholder'=>'form.email')))
            ->add('lastname', TextType::class, array('label_attr'=>array('hidden'=>'hidden'),
                'attr'=>array('placeholder'=>'Lastname')))
            ->add('firstname', TextType::class, array('label_attr'=>array('hidden'=>'hidden'),
                'attr'=>array('placeholder'=>'Firstname')))
            ->add('subject', TextType::class, array('label_attr'=>array('hidden'=>'hidden'),
                'attr'=>array('placeholder'=>'Subject')))
            ->add('message', TextareaType::class, array('label_attr'=>array('hidden'=>'hidden'),
                'attr'=>array('placeholder'=>'your message', 'rows' => 6)));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Contact::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contact';
    }


}
