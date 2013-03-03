<?php
/**
 * Wesrc Copyright 2013
 * Modifying, copying, of code contained herein that is not specifically
 * authorized by Wesrc UG ("Company") is strictly prohibited.
 * Violators will be prosecuted.
 *
 * This restriction applies to proprietary code developed by WsSrc. Code from
 * third-parties or open source projects may be subject to other licensing
 * restrictions by their respective owners.
 *
 * Additional terms can be found at http://www.wesrc.com/company/terms
 *
 * PHP Version 5
 *
 * @category Factory
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Service\Factory;

use Secretery\Service\Mail;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * MailFactory
 *
 * @category Factory
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class MailFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $sl
     * @return \Secretery\Service\Mail
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $config       = $sl->get('config');
        $translator   = $sl->get('translator');
        $transport    = $config['mail']['transport'];
        $host         = $config['mail']['domain_url'];
        $defaultEmail = $config['mail']['default_email'];
        $defaultFrom  = $config['mail']['default_from'];
        $SxMail       = new \SxMail\SxMail(
            $sl->get('view_manager')->getRenderer(), array('transport' => $transport)
        );
        return new Mail($SxMail, $translator, $host, $defaultFrom, $defaultEmail);
    }
}