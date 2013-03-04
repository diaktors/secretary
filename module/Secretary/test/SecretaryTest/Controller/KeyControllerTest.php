<?php

namespace SecretaryTest\Controller;

class KeyControllerTest extends AuthController
{
    public function testIndexActionCannotBeAccessed()
    {
        $this->dispatch('/secretary/key');
        $this->assertResponseStatusCode(403);
        $this->assertModuleName('secretary');
        $this->assertControllerName('secretary\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretary/default');
        $this->assertActionName('index');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretary');
        $this->assertQueryContentContains("*/h1", '403 Forbidden');
    }

    public function testAddActionCannotBeAccessed()
    {
        $this->dispatch('/secretary/key/add');
        $this->assertResponseStatusCode(403);
        $this->assertModuleName('secretary');
        $this->assertControllerName('secretary\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretary/default');
        $this->assertActionName('add');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretary');
        $this->assertQueryContentContains("*/h1", '403 Forbidden');
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->injectUserAuth();

        $this->dispatch('/secretary/key');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('secretary');
        $this->assertControllerName('secretary\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretary/default');
        $this->assertActionName('index');
        $this->assertQuery("*/a[@class='brand']");
        $this->assertQuery("*/form[@id='keyForm']");
        $this->assertQuery("*/input[@name='passphrase']");
        $this->assertQuery("*/a[@href='/secretary/key/index']");
        $this->assertQueryContentContains("*/a[@class='brand']", 'Secretary');
    }

    public function testAddActionCanBePosted()
    {
        /*$this->injectUserAuth();

        $this->dispatch('/secretary/key/add', 'POST', array('passphrase' => '123456'));

        \Zend\Debug\Debug::dump($this->getResponse());
        exit();

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('secretary');
        $this->assertControllerName('secretary\controller\key');
        $this->assertControllerClass('keycontroller');
        $this->assertMatchedRouteName('secretary/default');
        $this->assertActionName('index');*/
        //$this->assertQuery("*/a[@class='brand']");
        //$this->assertQuery("*/form[@id='keyForm']");
        //$this->assertQuery("*/input[@name='passphrase']");
        //$this->assertQuery("*/a[@href='/secretary/key/index']");
        //$this->assertQueryContentContains("*/a[@class='brand']", 'Secretary');
    }
}