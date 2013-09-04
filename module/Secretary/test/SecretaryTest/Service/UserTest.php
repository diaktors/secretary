<?php

namespace SecretaryTest\Service;

use Secretary\Service\User as UserService;

class UserTest extends Base
{
    /**
     * @var \Secretary\Service\User
     */
    protected $userService;

    protected function setUp()
    {
        parent::setUp();
        $this->userService = new UserService();
        $this->userService->setEntityManager($this->em);
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->userService);
    }

    public function testGetUserById()
    {
        $checkUser = $this->createUser();
        $user      = $this->userService->getUserById(1);
        $this->assertInstanceOf('\Secretary\Entity\User', $user);
        $this->assertSame($checkUser->getId(), $user->getId());
        $this->assertSame($checkUser->getEmail(), $user->getEmail());
        $this->assertSame($checkUser->getLanguage(), $user->getLanguage());
        $this->assertSame($checkUser->getDisplayName(), $user->getDisplayName());
    }

    public function testUpdateUserSettings()
    {
        $user   = $this->createUser();
        $values = array(
            'display_name'  => 'olala',
            'language'      => 'de_DE',
            'notifications' => false
        );
        $user = $this->userService->updateUserSettings($user, $values);

        $this->assertInstanceOf('\Secretary\Entity\User', $user);
        $this->assertSame($values['display_name'], $user->getDisplayName());
        $this->assertSame($values['language'], $user->getLanguage());
        $this->assertSame($values['notifications'], $user->getNotifications());

    }

    /**
     * @return \Secretary\Entity\User
     */
    protected function createUser()
    {
        $role = $this->em->getRepository('Secretary\Entity\Role')->findOneBy(array(
            'roleId' => 'user'
        ));
        $user = new \Secretary\Entity\User();
        $user->setDisplayName('Foo Bar')
            ->setEmail('foo@bar.com')
            ->setLanguage('en_US')
            ->setPassword('abc?123')
            ->addRole($role);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

}