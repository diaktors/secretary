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
 * @category Entity
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use ZfcUser\Entity\UserInterface;
use BjyAuthorize\Provider\Role\ProviderInterface;
//use Doctrine\Common\Persistence\PersistentObject;

/**
 * User Entity
 *
 * @category Entity
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 *
 * @ORM\Table(
 *   name="user",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="username", columns={"username"}),
 *     @ORM\UniqueConstraint(name="email", columns={"email"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Secretery\Entity\Repository\User")
 */
class User implements UserInterface, ProviderInterface
{
    /**
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="username", type="string", nullable=true)
     */
    protected $username;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @ORM\Column(name="password", type="string", length=128)
     */
    protected $password;

    /**
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @ORM\OneToOne(targetEntity="Key", mappedBy="user")
     */
    protected $key;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime")
     */
    protected $dateUpdated;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Secretery\Entity\Role", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="user2role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="User2Note", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $user2note;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="user2group",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param  string $displayName
     * @return UserInterface
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param  int $state
     * @return UserInterface
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get role.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role to the user.
     *
     * @param  Role $role
     * @return void
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;
    }

    /**
     * Get User2Note collection
     *
     * @return ArrayCollection
     */
    public function getUser2Note()
    {
        return $this->user2note;
    }

    /**
     * Add user 2 note relation
     *
     * @param  User2Note $user2note
     * @return $this
     */
    public function addUser2Note(User2Note $user2note)
    {
        $this->getUser2Note()->add($user2note);
        return $this;
    }

    /**
     * Get Key Relation
     *
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get groups.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add group relation
     *
     * @param  Gruop $group
     * @return $this
     */
    public function addGroup(Group $group)
    {
        $this->getGroups()->add($group);
        return $this;
    }


    /**
     * return void
     */
    public function __construct()
    {
        $this->groups    = new ArrayCollection();
        $this->roles     = new ArrayCollection();
        $this->user2note = new ArrayCollection();
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array                = get_object_vars($this);
        $array['dateCreated'] = $array['dateCreated']->format('Y-m-d H:i:s');
        $array['dateUpdated'] = $array['dateUpdated']->format('Y-m-d H:i:s');
        unset($array['key']);
        unset($array['user2note']);
        return $array;
    }
}