<?php

namespace SecreteryTest\Controller;

class UserControllerTest extends AuthController
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(__DIR__) . '/../../../../config/test/application.config.php'
        );
        parent::setUp();
    }

    public function testLoginActionCanBeAccessed()
    {
        $this->dispatch('/user/login');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('zfcuser');
        $this->assertControllerName('zfcuser');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('zfcuser/login');
        $this->assertActionName('login');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
        $this->assertQuery("*/form[@action='/user/login']");
        $this->assertQuery("*/input[@name='identity']");
        $this->assertQuery("*/input[@name='credential']");
    }

    public function testRegisterActionCanBeAccessed()
    {
        $this->dispatch('/user/register');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('zfcuser');
        $this->assertControllerName('zfcuser');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('zfcuser/register');
        $this->assertActionName('register');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
        $this->assertQuery("*/form[@action='/user/register']");
        $this->assertQuery("*/input[@name='email']");
        $this->assertQuery("*/input[@name='display_name']");
        $this->assertQuery("*/input[@name='password']");
        $this->assertQuery("*/input[@name='passwordVerify']");
    }
}