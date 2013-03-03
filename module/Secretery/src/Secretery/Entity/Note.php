<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 5
 *
 * @category Entity
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 */

namespace Secretery\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Persistence\PersistentObject;
use Zend\Form\Annotation;

/**
 * Key Entity
 *
 * @category Entity
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretery
 *
 * @ORM\Table(name="note")
 * @ORM\Entity(repositoryClass="Secretery\Entity\Repository\Note")
 * @Annotation\Name("noteForm")
 * @Annotation\Attributes({"id":"noteForm"})
 */
class Note //extends PersistentObject
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Annotation\Attributes({"type":"hidden"})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="title")
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Note title"})
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Attributes({"class":"hide", "id": "contentPlaceholder", "required":false})
     * @Annotation\AllowEmpty({"allowEmpty":"true"})
     * @Annotation\Options({"label":"Note text"})
     */
    protected $content;

    /**
     * @var bool
     * @ORM\Column(name="private", type="boolean")
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Options({"label":"Note private"})
     * @Annotation\Attributes({"label":"Note private"})
     * @Annotation\AllowEmpty({"allowEmpty":"false"})
     * @Annotation\Required({"required":"false"})
     */
    protected $private = true;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="notes")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Attributes({"id":"groupHidden"})
     * @Annotation\AllowEmpty({"allowEmpty":"false"})
     */
    protected $group;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", type="datetime")
     * @Annotation\Exclude()
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", type="datetime")
     * @Annotation\Exclude()
     */
    protected $dateUpdated;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="User2Note", mappedBy="note", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="note_id")
     * @Annotation\Exclude()
     */
    protected $user2note;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Save Note", "class": "btn btn-primary"})
     */
    protected $submit;

    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Attributes({"id":"membersHidden"})
     * @Annotation\AllowEmpty({"allowEmpty":"false"})
     */
    protected $members;

    /**
     * Set Id
     *
     * @param  int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set Title
     *
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set Content
     *
     * @param  string $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set Private
     *
     * @param  bool $private
     * @return self
     */
    public function setPrivate($private)
    {
        $this->private = $private;
        return $this;
    }

    /**
     * Set Group
     *
     * @param  Group $group
     * @return self
     */
    public function setGroup($group)
    {
        if ($group instanceof Group) {
            $this->group = $group;
        } else {
            $this->group = null;
        }
        return $this;
    }


    /**
     * Add User2Note relation
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
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
     * return void
     */
    public function __construct()
    {
        $this->user2note = new ArrayCollection();
    }

    /**
     * Populate from an array.
     *
     * @param  array $data
     * @return void
     */
    public function populate(array $data)
    {
        $this->title   = $data['title'];
        $this->content = $data['content'];
        $this->private = $data['private'];
        return;
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
        $array['group']       = $array['group']->getIdentity();
        unset($array['user2note']);
        return $array;
    }

}