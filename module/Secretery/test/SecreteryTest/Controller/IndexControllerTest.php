<?php

namespace SecreteryTest\Controller;

class IndexControllerTest extends AuthController
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include dirname(__DIR__) . '/../../../../config/test/application.config.php'
        );
        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Secretery');
        $this->assertControllerName('secretery\controller\index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
        $this->assertActionName('index');
        $this->assertQuery("*/div[@class='hero-unit']");
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
        $this->assertQuery("*/form[@action='/user/login']");
        $this->assertQuery("*/input[@name='identity']");
        $this->assertQuery("*/input[@name='credential']");
    }
}