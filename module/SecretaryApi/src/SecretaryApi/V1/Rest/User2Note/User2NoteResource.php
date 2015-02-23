<?php
namespace SecretaryApi\V1\Rest\User2Note;

use Secretary\Entity;
use Secretary\Service;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;
use ZF\ApiProblem\ApiProblem;

class User2NoteResource extends DoctrineResource
{
    /**
     * Fetch a user2note
     *
     * @param int $noteId
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function fetch($noteId)
    {
        /** @var Service\User $userService */
        $userService = $this->getServiceManager()->get('user-service');
        $user = $userService->getUserByMail($this->getIdentity()->getName());

        $this->setEntityIdentifierName('userId.noteId');

        return parent::fetch($user->getId(). '.' . $noteId);
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @throws \InvalidArgumentException
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        if (empty($data->noteId) || !is_numeric($data->noteId)) {
            return new ApiProblem(422, 'noteId value missing');
        }
        if (empty($data->userId) || !is_numeric($data->userId)) {
            return new ApiProblem(422, 'userId value missing');
        }

        $data->user = $data->userId;
        $data->note = $data->noteId;
        $this->setEntityIdentifierName('userId.noteId');

        return parent::create($data);
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  int  $noteId
     * @param  stdClass $data
     * @return ApiProblem|mixed
     */
    public function patch($noteId, $data)
    {
        if (empty($data->userId) || !is_numeric($data->userId)) {
            return new ApiProblem(422, 'userId value missing');
        }

        $this->setEntityIdentifierName('userId.noteId');

        return parent::patch($data->userId . '.' . $noteId, $data);
    }
}
