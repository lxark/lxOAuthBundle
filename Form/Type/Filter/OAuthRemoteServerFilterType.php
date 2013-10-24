<?php

namespace LX\OAuthBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

/**
 * OAuthRemoteServer form filter type
 *
 * @author Alix Chaysinh <alix.chaysinh@gmail.com>
 * @since  2013-10-18
 */
class OAuthRemoteServerFilterType extends AbstractType
{
    const FORM_NAME = 'oauth_remote_server_filter';

    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options A list of options
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-10-18
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Name
            ->add('name', 'text', array(
                'required'    => false,
                'label'       => 'Name',
            ))
            // Base url
            ->add('baseUrl', 'text', array(
                'required'    => false,
                'label'       => 'Base url',
            ))
            // Type
            ->add('type', 'oauth_remote_server_type', array(
                'required'    => false,
                'label'       => 'Type',
                'empty_value' => '',
                'multiple'    => true,
                'attr'        => array(
                    'data-toggle' => 'multiselect'
                )
            ))
            // Status
            ->add('status', 'oauth_remote_server_status', array(
                'required'    => false,
                'label'       => 'Status',
                'empty_value' => '',
                'multiple'    => true,
                'attr'        => array(
                    'data-toggle' => 'multiselect'
                )
            ))
            ->remove('consumerKey')
            ->remove('consumerSecret');
    }

    /**
     * Set parent
     *
     * @return string
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-10-18
     */
    public function getParent()
    {
        return 'oauth_remote_server';
    }

    /**
     * Get the form name
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
