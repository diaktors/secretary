<?php

namespace SecreteryTest\Controller;

class KeyControllerTest extends AuthController
{
    public function testIndexActionCannotBeAccessed()
    {
        $this->dispatch('/secretery/key');
        $this->assertResponseStatusCode(403);
        $this->assertModuleName('secretery');
        $this->assertControllerName('secretery\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretery/default');
        $this->assertActionName('index');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
        $this->assertQueryContentContains("*/h1", '403 Forbidden');
    }

    public function testAddActionCannotBeAccessed()
    {
        $this->dispatch('/secretery/key/add');
        $this->assertResponseStatusCode(403);
        $this->assertModuleName('secretery');
        $this->assertControllerName('secretery\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretery/default');
        $this->assertActionName('add');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
        $this->assertQueryContentContains("*/h1", '403 Forbidden');
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->injectUserAuth();

        $this->dispatch('/secretery/key');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('secretery');
        $this->assertControllerName('secretery\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretery/default');
        $this->assertActionName('index');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQuery("*/form[@id='keyForm']");
        $this->assertQuery("*/input[@name='passphrase']");
        $this->assertQuery("*/a[@href='/secretery/key/index']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
    }

    public function testAddActionCanBePosted()
    {
        /*$this->injectUserAuth();

        $this->dispatch('/secretery/key/add', 'POST', array('passphrase' => '123456'));

        \Zend\Debug\Debug::dump($this->getResponse());
        exit();

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('secretery');
        $this->assertControllerName('secretery\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretery/default');
        $this->assertActionName('index');*/
        //$this->assertQuery("*/a[@class='brand']");
        //$this->assertQuery("*/form[@id='keyForm']");
        //$this->assertQuery("*/input[@name='passphrase']");
        //$this->assertQuery("*/a[@href='/secretery/key/index']");
        //$this->assertQueryContentContains("*/a[@class='brand']", 'Secretery');
    }
}