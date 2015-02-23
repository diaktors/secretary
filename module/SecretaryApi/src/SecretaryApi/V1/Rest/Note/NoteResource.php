<?php
namespace SecretaryApi\V1\Rest\Note;

use Secretary\Entity;
use Secretary\Service;
use Zend\EventManager\StaticEventManager;
use Zend\Stdlib\ArrayUtils;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;
use ZF\ApiProblem\ApiProblem;

/**
 * Class NoteResource
 */
class NoteResource extends DoctrineResource
{
}
