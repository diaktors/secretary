<?php

namespace SecretaryTest\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp()
    {
        parent::setUp();
        $this->setupDoctrine();
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->em);
    }

    /**
     * @return EntityManager
     */
    protected function setupDoctrine()
    {
        $serviceManager = new \Zend\ServiceManager\ServiceManager(
            new \Zend\Mvc\Service\ServiceManagerConfig(array())
        );
        $serviceManager->setService('ApplicationConfig', $this->getTestConfig());
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->get('Application')->bootstrap();

        $this->em = $serviceManager->get('doctrine.entitymanager.orm_default');
        self::createDbSchema($this->em);
        return $this->em;
    }

    /**
     * @return array
     */
    protected function getTestConfig()
    {
        return include __DIR__ . '/../../../../../config/test/application.config.php';;
    }

    /**
     * @param EntityManager $em
     */
    static public function createDbSchema(EntityManager $em)
    {
        $tool    = new SchemaTool($em);
        $classes = array(
            $em->getClassMetadata('Secretary\Entity\User'),
            $em->getClassMetadata('Secretary\Entity\Role'),
            $em->getClassMetadata('Secretary\Entity\Key'),
            $em->getClassMetadata('Secretary\Entity\Note'),
            $em->getClassMetadata('Secretary\Entity\User2Note'),
        );

        $tool->createSchema($classes);

        $guestRole = new \Secretary\Entity\Role();
        $guestRole->setId(1)
            ->setDefault(true)
            ->setRoleId('guest');

        $userRole = new \Secretary\Entity\Role();
        $userRole->setId(2)
            ->setDefault(false)
            ->setRoleId('user');

        $keyUserRole = new \Secretary\Entity\Role();
        $keyUserRole->setId(3)
            ->setDefault(false)
            ->setParent($userRole)
            ->setRoleId('keyuser');

        $adminUserRole = new \Secretary\Entity\Role();
        $adminUserRole->setId(4)
            ->setDefault(false)
            ->setParent($keyUserRole)
            ->setRoleId('admin');

        $em->persist($guestRole);
        $em->persist($userRole);
        $em->persist($keyUserRole);
        $em->persist($adminUserRole);
        $em->flush();
    }



}