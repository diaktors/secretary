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

use Secretery\Service\Note;
use Secretery\Service\Encryption;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * NoteFactory
 *
 * @category Factory
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class NoteFactory implements FactoryInterface
{
    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $sl
     * @return \Secretery\Service\Note
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $service = new Note();
        /* @var \Doctrine\Orm\EntityManager $em */
        $em = $sl->get('doctrine.entitymanager.orm_default');
        /* @var \Secretery\Service\Encryption $encService */
        $encService = $sl->get('encryption-service');
        $service->setEntityManager($em);
        $service->setEncryptionService($encService);
        return $service;
    }
}