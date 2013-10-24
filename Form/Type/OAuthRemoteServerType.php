<?php

namespace LX\OAuthBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * OAuthRemoteServer form type
 *
 * @author Alix Chaysinh <alix.chaysinh@gmail.com>
 * @since  2013-05-23 16:48
 */
class OAuthRemoteServerType extends AbstractType
{
    const FORM_NAME = 'oauth_remote_server';

    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options A list of options
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since 2013-05-23 16:48
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Name
            ->add('name', 'text', array(
                'required'    => true,
                'label'       => 'Name',
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            // Base url
            ->add('baseUrl', 'text', array(
                'required'    => true,
                'label'       => 'Base url',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Url(),
                )
            ))
            // Type
            ->add('type', 'oauth_remote_server_type', array(
                'required' => true,
                'label'    => 'Type',
                'attr'     => array(
                    'data-toggle' => 'autocomplete-combobox'
                )
            ))
            // Status
            ->add('status', 'oauth_remote_server_status', array(
                'required' => true,
                'label'    => 'Status',
                'attr'     => array(
                    'data-toggle' => 'autocomplete-combobox'
                )
            ))
            // Consumer key
            ->add('consumerKey', 'text', array(
                'required'    => true,
                'label'       => 'Consumer key',

                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ))
            // Consumer secret
            ->add('consumerSecret', 'text', array(
                'required'    => true,
                'label'       => 'Consumer secret',
                'constraints' => array(
                    new Constraints\NotBlank()
                )
            ));
    }

    /**
     * Retrieve the form name
     *
     * @return string
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-10-18
     */
    public function getName()
    {
        return self::FORM_NAME;
    }
}
