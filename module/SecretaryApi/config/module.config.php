<?php
return array(
    'router' => array(
        'routes' => array(
            'secretaryapi.rest.doctrine.group' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/group[/:group_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\Group\\Controller',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/user[/:user_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.note' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/note[/:note_id/user/:user_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\Note\\Controller',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.user2-note' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/user2note[/:user2note_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\User2Note\\Controller',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.key' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/key[/:key_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\Key\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'secretaryapi.rest.doctrine.group',
            1 => 'secretaryapi.rest.doctrine.user',
            2 => 'secretaryapi.rest.doctrine.note',
            3 => 'secretaryapi.rest.doctrine.user2-note',
            4 => 'secretaryapi.rest.doctrine.key',
        ),
    ),
    'service_manager' => array(
        'factories' => array(),
    ),
    'zf-rest' => array(
        'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\Group\\GroupResource',
            'route_name' => 'secretaryapi.rest.doctrine.group',
            'route_identifier_name' => 'group_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'group',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => 25,
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\Group',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\Group\\GroupCollection',
        ),
        'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\User\\UserResource',
            'route_name' => 'secretaryapi.rest.doctrine.user',
            'route_identifier_name' => 'user_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => 25,
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\User',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\User\\UserCollection',
        ),
        'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\Note\\NoteResource',
            'route_name' => 'secretaryapi.rest.doctrine.note',
            'route_identifier_name' => 'note_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'note',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => 25,
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\Note',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\Note\\NoteCollection',
        ),
        'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteResource',
            'route_name' => 'secretaryapi.rest.doctrine.user2-note',
            'route_identifier_name' => 'user2note_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'user2_note',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => 25,
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\User2Note',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteCollection',
        ),
        'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\Key\\KeyResource',
            'route_name' => 'secretaryapi.rest.doctrine.key',
            'route_identifier_name' => 'key_id',
            'entity_identifier_name' => 'userId',
            'collection_name' => 'key',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => 25,
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\Key',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\Key\\KeyCollection',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => 'HalJson',
            'SecretaryApi\\V1\\Rest\\User\\Controller' => 'HalJson',
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => 'HalJson',
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => 'HalJson',
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
                0 => 'application/json',
                1 => 'application/*+json',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
                0 => 'application/json',
                1 => 'application/*+json',
            ),
        ),
        'content_type_whitelist' => array(
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
                0 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
                0 => 'application/json',
            ),
        ),
        'accept-whitelist' => array(
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content-type-whitelist' => array(
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
                0 => 'application/vnd.secretary.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Secretary\\Entity\\Group' => array(
                'route_identifier_name' => 'group_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.group',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Group\\GroupHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\Group\\GroupCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.group',
                'is_collection' => true,
            ),
            'Secretary\\Entity\\User' => array(
                'route_identifier_name' => 'user_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.user',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User\\UserHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.user',
                'is_collection' => true,
            ),
            'Secretary\\Entity\\Note' => array(
                'route_identifier_name' => 'note_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.note',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Note\\NoteHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\NoteCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.note',
                'is_collection' => true,
            ),
            'Secretary\\Entity\\User2Note' => array(
                'route_identifier_name' => 'user2note_id',
                'entity_identifier_name' => 'noteId',
                'route_name' => 'secretaryapi.rest.doctrine.user2-note',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteCollection' => array(
                'entity_identifier_name' => 'noteId',
                'route_name' => 'secretaryapi.rest.doctrine.user2-note',
                'is_collection' => true,
            ),
            'Secretary\\Entity\\Key' => array(
                'route_identifier_name' => 'key_id',
                'entity_identifier_name' => 'userId',
                'route_name' => 'secretaryapi.rest.doctrine.key',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Key\\KeyHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\KeyCollection' => array(
                'entity_identifier_name' => 'userId',
                'route_name' => 'secretaryapi.rest.doctrine.key',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-apigility' => array(
        'doctrine-connected' => array(
            'SecretaryApi\\V1\\Rest\\Group\\GroupResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Group\\GroupHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\User\\UserResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User\\UserHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\NoteResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Note\\NoteHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\Key\\KeyResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Key\\KeyHydrator',
            ),
        ),
    ),
    'doctrine-hydrator' => array(
        'SecretaryApi\\V1\\Rest\\Group\\GroupHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\Group',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => array(
                'users' => 'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection',
                'notes' => 'ZF\\Apigility\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink',
            ),
            'use_generated_hydrator' => true,
        ),
        'SecretaryApi\\V1\\Rest\\User\\UserHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\User',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(
                'groups' => 'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection',
                'roles' => 'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection',
                'user2note' => 'ZF\\Apigility\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink',
                'key' => 'ZF\\Apigility\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink',
                //'key' => 'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField',
            ),
            'use_generated_hydrator' => null,
        ),
        'SecretaryApi\\V1\\Rest\\Note\\NoteHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\Note',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(
                'user2note' => 'ZF\\Apigility\\Doctrine\\Server\\Hydrator\\Strategy\\CollectionLink',
            ),
            'use_generated_hydrator' => null,
        ),
        'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\User2Note',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(),
            'use_generated_hydrator' => null,
        ),
        'SecretaryApi\\V1\\Rest\\Key\\KeyHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\Key',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(),
            'use_generated_hydrator' => null,
        ),
    ),
    'zf-content-validation' => array(
        'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
            'input_filter' => 'SecretaryApi\\V1\\Rest\\Group\\Validator',
        ),
        'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'SecretaryApi\\V1\\Rest\\User\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'SecretaryApi\\V1\\Rest\\Group\\Validator' => array(
            0 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                        'options' => array(),
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\I18n\\Validator\\Alnum',
                        'options' => array(
                            'allowwhitespace' => true,
                        ),
                    ),
                    1 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => '2',
                            'max' => '255',
                        ),
                    ),
                ),
                'description' => 'name of the group',
                'allow_empty' => false,
                'error_message' => 'Please provide a valid "name" value',
                'continue_if_empty' => false,
            ),
        ),
        'SecretaryApi\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'name' => 'email',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                        'options' => array(),
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\EmailAddress',
                        'options' => array(),
                    ),
                ),
                'description' => 'Email of User',
                'error_message' => 'Please provide a valid email',
            ),
            1 => array(
                'name' => 'language',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
    ),
);
