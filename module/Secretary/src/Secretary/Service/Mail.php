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
 * @category Service
 * @package  Secretary
 * @author   Sergio Hermes <hermes.sergio@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @version  GIT: <git_id>
 * @link     https://github.com/wesrc/secretary
 */

namespace Secretary\Service;

use Secretary\Entity;
use SxMail\SxMail;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\ViewModel;

/**
 * Mail Service
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
     * @var Translator
     */
    protected $translator;

    /**
     * @param \SxMail\SxMail $SxMail
     * @param Translator $translator
     * @param string $host
     * @param string $defaultFrom
     * @param string $defaultEmail
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
        $subject = $this->translator->translate('Secretary - Ooops, an error occured');
        $message->addTo($this->defaultEmail);
        $message->addFrom($this->defaultFrom);
        $message->setSubject($subject);

        $this->SxMail->send($message);
        return;
    }

    /**
     * @param  array $mailOptions
     * @return void
     */
    protected function sendMail(array $mailOptions)
    {
        $to = $this->defaultEmail;
        if (!empty($mailOptions['to'])) {
            $to = $mailOptions['to'];
        }
        $subject = $this->translator->translate('A new message from your Secretary');
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

        $subject = $this->translator->translate('Secretary - New user registration');
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

        $subject = 'Secretary - New group note';
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

        $subject = 'Secretary - Group note edited';
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
     * @param  Entity\Note $note
     * @param  array $users
     * @param  Entity\User $owner
     * @param  string $subject
     * @param  string $title
     * @return void
     */
    protected function sendNoteGroupMail(
        Entity\Note $note,
        array $users,
        Entity\User $owner,
        $subject,
        $title
    ) {
        /** @var Entity\User $user */
        foreach ($users as $user) {
            if ($user->getId() != $owner->getId() && true === $user->getNotifications()) {
                $this->translator->setLocale($user->getLanguage());
                $subject = $this->translator->translate($subject);
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
        // @todo this will only work, as long owner is the only one with edit permissions
        $this->translator->setLocale($owner->getLanguage());
        return;
    }

}