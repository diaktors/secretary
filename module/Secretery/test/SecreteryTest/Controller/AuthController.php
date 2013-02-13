<?php

namespace SecreteryTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AuthController extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(__DIR__) . '/../../../../config/test/application.config.php'
        );
        parent::setUp();
    }

    protected function injectUserAuth()
    {
        $ZfcUserMock = $this->getMock('Secretery\Entity\User');
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

}