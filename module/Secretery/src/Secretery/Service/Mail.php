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
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @link     http://www.wesrc.com
 */

namespace Secretery\Service;

use SxMail\SxMail;
use Zend\I18n\Translator\Translator;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

/**
 * Logger Service
 *
 * @category Service
 * @package  Secretery
 * @author   Michael Scholl <michael@wesrc.com>
 * @license  http://www.wesrc.com/company/terms Terms of Service
 * @version  Release: @package_version@
 * @link     http://www.wesrc.com
 */
class Mail
{
    /**
     * @var \SxMail\SxMail
     */
    protected $SxMail;

    /**
     * @var string
     */
    protected $defaultEmail;

    /**
     * @var string
     */
    protected $defaultFrom;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @param \SxMail\SxMail                   $SxMail
     * @param \Zend\I18n\Translator\Translator $translator
     * @param string                           $host
     * @param string                           $defaultFrom
     * @param string                           $defaultEmail
     */
    public function __construct(SxMail $SxMail, Translator $translator, $host, $defaultFrom, $defaultEmail)
    {
        $SxMail->setLayout('mail/layout.phtml');

        $translator->addTranslationFilePattern(
            'gettext', __DIR__ . '/../../../language', 'mail-%s.mo'
        );

        $this->SxMail       = $SxMail;
        $this->host         = trim($host, '/');
        $this->translator   = $translator;
        $this->defaultFrom  = $defaultFrom;
        $this->defaultEmail = $defaultEmail;
    }

    /**
     * @param  array  $mailOptions
     * @param  string $target
     * @return void
     */
    public function send(array $mailOptions, $target)
    {
        switch (strtolower($target)) {
            case 'error':
                $this->sendError($mailOptions);
                break;
            case 'note-add':
                $this->sendNoteAdd($mailOptions);
                break;
            case 'note-edit':
                $this->sendNoteEdit($mailOptions);
                break;
            case 'registration':
                $this->sendRegistration($mailOptions);
                break;
            default:
                $this->sendMail($mailOptions);
                break;
        }

        return;
    }

    /**
     * @param  array $mailOptions
     * @return void
     */
    protected function sendError(array $mailOptions)
    {
        $exception = 'No exception info given';
        if (!empty($mailOptions['exception'])) {
            $exception = $mailOptions['exception'];
        }

        $content = new ViewModel();
        $content->setTemplate('mail/error.phtml')
            ->setVariable('exception', $exception);

        $message = $this->SxMail->compose($content);
        $subject = $this->translator->translate('Secretery - Ooops, an error occured');
        $message->addTo($this->defaultEmail);
        $message->addFrom($this->defaultFrom);
        $message->setSubject($subject);

        $this->SxMail->send($message);
        return;
    }

    /**
     * @param  array $mailOptions
     * @return \Zend\Mail\Message
     */
    protected function sendMail(array $mailOptions)
    {
        $to = $this->defaultEmail;
        if (!empty($mailOptions['to'])) {
            $to = $mailOptions['to'];
        }
        $subject = $this->translator->translate('A new message from your Secretery');
        if (!empty($mailOptions['subject'])) {
            $subject = $mailOptions['subject'];
        }
        $body = '';
        if (!empty($mailOptions['body'])) {
            $body = $mailOptions['body'];
        }

        $this->SxMail->setLayout('mail/layoutPlain.phtml');
        $message = $this->SxMail->compose($body, 'text/plain');
        $message->addTo($to);
        $message->addFrom($this->defaultFrom);
        $message->setSubject($subject);

        $this->SxMail->send($message);
        return;
    }

    /**
     * @param  array $mailOptions
     * @return void
     * @throws \InvalidArgumentException If user param is missing in mail array
     */
    protected function sendRegistration(array $mailOptions)
    {
        if (empty($mailOptions['user'])) {
            throw new \InvalidArgumentException('User param is missing');
        }

        $content = new ViewModel();
        $content->setTemplate('mail/registration.phtml')
            ->setVariable('user', $mailOptions['user']);

        $message = $this->SxMail->compose($content);

        $subject = $this->translator->translate('Secretery - New user registration');
        $message->addTo($this->defaultEmail);
        $message->addFrom($this->defaultFrom);
        $message->setSubject($subject);

        $this->SxMail->send($message);
        return;
    }

    /**
     * @param  array $mailOptions
     * @return void
     * @throws \InvalidArgumentException If note, users, owner param is missing in mail array
     */
    protected function sendNoteAdd(array $mailOptions)
    {
        if (empty($mailOptions['note'])) {
            throw new \InvalidArgumentException('Note param is missing');
        }
        if (empty($mailOptions['users'])) {
            throw new \InvalidArgumentException('Users param is missing');
        }
        if (empty($mailOptions['owner'])) {
            throw new \InvalidArgumentException('Owner param is missing');
        }

        $subject = $this->translator->translate('Secretery - New group note');
        $title   = 'A new note was added';
        $this->sendNoteGroupMail(
            $mailOptions['note'],
            $mailOptions['users'],
            $mailOptions['owner'],
            $subject,
            $title
        );

        return;
    }

    /**
     * @param  array $mailOptions
     * @return void
     * @throws \InvalidArgumentException If note, users, owner param is missing in mail array
     */
    protected function sendNoteEdit(array $mailOptions)
    {
        if (empty($mailOptions['note'])) {
            throw new \InvalidArgumentException('Note param is missing');
        }
        if (empty($mailOptions['users'])) {
            throw new \InvalidArgumentException('Users param is missing');
        }
        if (empty($mailOptions['owner'])) {
            throw new \InvalidArgumentException('Owner param is missing');
        }

        $subject = $this->translator->translate('Secretery - Group note edited');
        $title   = 'Note was edited';
        $this->sendNoteGroupMail(
            $mailOptions['note'],
            $mailOptions['users'],
            $mailOptions['owner'],
            $subject,
            $title
        );

        return;
    }

    /**
     * @param  \Secretery\Entity\Note $note
     * @param  array                  $users
     * @param  \Secretery\Entity\User $owner
     * @param  string $subject
     * @param  string $title
     * @return void
     */
    protected function sendNoteGroupMail(\Secretery\Entity\Note $note, array $users,
                                         $owner, $subject, $title)
    {
        /**
         * @var \Secretery\Entity\User $user
         * @var \Secretery\Entity\User $owner
         */
        foreach ($users as $user) {
            if ($user->getId() != $owner->getId()) {
                $content = new ViewModel();
                $content->setTemplate('mail/note.phtml')
                    ->setVariable('title', $title)
                    ->setVariable('note', $note)
                    ->setVariable('host', $this->host);

                $message = $this->SxMail->compose($content);
                $message->addTo($user->getEmail());
                $message->addFrom($this->defaultFrom);
                $message->setSubject($subject);
                $this->SxMail->send($message);
            }
        }
        return;
    }

}