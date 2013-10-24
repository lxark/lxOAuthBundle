<?php

namespace LX\OAuthBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * OAuthRemoteServer form statuses type
 *
 * @author Alix Chaysinh <alix.chaysinh@gmail.com>
 * @since  2013-10-18
 */
class OAuthRemoteServerStatusType extends AbstractType
{
    const FORM_NAME = 'oauth_remote_server_status';

    /**
     * @var ObjectRepository $repository Repository
     */
    protected $repository;

    /**
     * Set repository
     *
     * @param ObjectRepository $repository
     *
     * @return self
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Get repository
     *
     * @return ObjectRepository
     */
    public function getRepository()
    {

        return $this->repository;
    }

    /**
     * Return statuses
     *
     * @return array
     */
    public function getChoices()
    {

        return ($this->repository) ? $this->repository->getStatuses() : array();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices()
        ));
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
        return 'choice';
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
