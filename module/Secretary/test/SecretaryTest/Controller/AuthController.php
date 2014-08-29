<?php

namespace SecretaryTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Doctrine\ORM\Tools\SchemaTool;

class AuthController extends AbstractHttpControllerTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(__DIR__) . '/../../../../config/test/application.config.php'
        );
        parent::setUp();
        $this->createDbSchema();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
        unset($this->em);
    }

    protected function injectUserAuth()
    {
        $ZfcUserMock = $this->getMock('Secretary\Entity\User');
        $ZfcUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('23'));

        $BjyAuthorizeMock = $this->getMock(
            'BjyAuthorize\Service\Authorize',
            array(),
            array(
                array(),
                $this->getApplicationServiceLocator()
            )
        );

        $BjyAuthorizeMock->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $BjyIdentityMock = $this->getMock('stdClass', array('getIdentityRoles'));
        $BjyIdentityMock->expects($this->any())
            ->method('getIdentityRoles')
            ->will($this->returnValue(array('user')));

        $BjyAuthorizeMock->expects($this->any())
            ->method('getIdentityProvider')
            ->will($this->returnValue($BjyIdentityMock));

        $authMock = $this->getMock('ZfcUser\Controller\Plugin\ZfcUserAuthentication');
        $authMock->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        $authMock->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($ZfcUserMock));

        $controllerPluginManager = $this->getApplicationServiceLocator()
            ->get('controllerpluginmanager');
        $controllerPluginManager->setService('zfcUserAuthentication', $authMock);
        $this->getApplicationServiceLocator()->get('servicemanager')
            ->setAllowOverride(true)
            ->setService('BjyAuthorize\Service\Authorize', $BjyAuthorizeMock);
    }

    protected function createDbSchema()
    {
        $this->em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.ormdefault');
        $tool     = new SchemaTool($this->em);
        $classes  = array(
            $this->em->getClassMetadata('Secretary\Entity\User'),
            $this->em->getClassMetadata('Secretary\Entity\Role'),
            $this->em->getClassMetadata('Secretary\Entity\Key'),
            $this->em->getClassMetadata('Secretary\Entity\Note'),
            $this->em->getClassMetadata('Secretary\Entity\User2Note'),
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

        $this->em->persist($guestRole);
        $this->em->persist($userRole);
        $this->em->persist($keyUserRole);
        $this->em->persist($adminUserRole);
        $this->em->flush();
    }

}