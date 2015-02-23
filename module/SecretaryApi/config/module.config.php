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
                    'constraints' => array(
                        'group_id' => '[0-9]*',
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
                    'constraints' => array(
                        'user_id' => '[0-9]*',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.note' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/note[/:note_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\Note\\Controller',
                    ),
                    'constraints' => array(
                        'note_id' => '[0-9]*',
                    ),
                ),
            ),
            'secretaryapi.rest.doctrine.user2note' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/user2note[/:note_id]',
                    'defaults' => array(
                        'controller' => 'SecretaryApi\\V1\\Rest\\User2Note\\Controller',
                    ),
                    'constraints' => array(
                        'note_id' => '[0-9]*',
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
                    'constraints' => array(
                        'key_id' => '[0-9]*',
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
            3 => 'secretaryapi.rest.doctrine.user2note',
            4 => 'secretaryapi.rest.doctrine.key',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'group-event-listener' => 'SecretaryApi\\V1\\Rest\\Group\\GroupEventListenerFactory',
            'note-event-listener' => 'SecretaryApi\\V1\\Rest\\Note\\NoteEventListenerFactory',
            'user2note-event-listener' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteEventListenerFactory',
        ),
    ),
    'zf-apigility-doctrine-query-provider' => array(
        'invokables' => array(
            'user2note_fetch_all' => 'SecretaryApi\\Query\\Provider\\User2Note\\FetchAll',
            'note_fetch' => 'SecretaryApi\\Query\\Provider\\Note\\Fetch',
            'note_fetch_all' => 'SecretaryApi\\Query\\Provider\\Note\\FetchAll',
            'user_fetch_all' => 'SecretaryApi\\Query\\Provider\\User\\FetchAll',
        ),
    ),
    'zf-rest' => array(
        'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\Group\\GroupResource',
            'route_name' => 'secretaryapi.rest.doctrine.group',
            'service_name' => 'Group',
            'route_identifier_name' => 'group_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'group',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
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
            'service_name' => 'User',
            'route_identifier_name' => 'user_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
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
            'service_name' => 'Note',
            'route_identifier_name' => 'note_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'note',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'query',
                1 => 'orderBy',
            ),
            'page_size' => '25',
            'page_size_param' => 'limit',
            'entity_class' => 'Secretary\\Entity\\Note',
            'collection_class' => 'SecretaryApi\\V1\\Rest\\Note\\NoteCollection',
        ),
        'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
            'listener' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteResource',
            'route_name' => 'secretaryapi.rest.doctrine.user2note',
            'service_name' => 'User2Note',
            'route_identifier_name' => 'note_id',
            'entity_identifier_name' => 'noteId',
            'collection_name' => 'user2note',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
            ),
            'collection_http_methods' => array(
                0 => 'POST',
                1 => 'GET',
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
            'service_name' => 'Key',
            'route_identifier_name' => 'key_id',
            'entity_identifier_name' => 'userId',
            'collection_name' => 'key',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(),
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
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/json',
                1 => 'application/*+json',
            ),
            'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/json',
                1 => 'application/*+json',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
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
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/json',
            ),
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
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
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
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
                'route_name' => 'secretaryapi.rest.doctrine.user2note',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteHydrator',
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteCollection' => array(
                'entity_identifier_name' => 'noteId',
                'route_name' => 'secretaryapi.rest.doctrine.user2note',
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
            'SecretaryApi\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'secretaryapi.rest.doctrine.user',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-apigility' => array(
        'doctrine-connected' => array(
            'SecretaryApi\\V1\\Rest\\Group\\GroupResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Group\\GroupHydrator',
                'listeners' => array(
                    0 => 'group-event-listener',
                ),
            ),
            'SecretaryApi\\V1\\Rest\\User\\UserResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User\\UserHydrator',
                'query_providers' => array(
                    'default' => 'default_orm',
                    'fetch_all' => 'user_fetch_all',
                ),
            ),
            'SecretaryApi\\V1\\Rest\\Note\\NoteResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\Note\\NoteHydrator',
                'listeners' => array(
                    0 => 'note-event-listener',
                ),
                'query_providers' => array(
                    'default' => 'default_orm',
                    'fetch' => 'note_fetch',
                    'fetch_all' => 'note_fetch_all',
                ),
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteResource' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'SecretaryApi\\V1\\Rest\\User2Note\\User2NoteHydrator',
                'listeners' => array(
                    0 => 'user2note-event-listener',
                ),
                'query_providers' => array(
                    'default' => 'default_orm',
                    'fetch_all' => 'user2note_fetch_all',
                ),
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
                'users' => 'DoctrineHydrationModule\\Strategy\\ODM\\MongoDB\\ReferencedCollection',
                'notes' => 'DoctrineHydrationModule\\Strategy\\ODM\\MongoDB\\ReferencedCollection',
            ),
            'use_generated_hydrator' => true,
        ),
        'SecretaryApi\\V1\\Rest\\User\\UserHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\User',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(),
            'use_generated_hydrator' => null,
        ),
        'SecretaryApi\\V1\\Rest\\Note\\NoteHydrator' => array(
            'entity_class' => 'Secretary\\Entity\\Note',
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => null,
            'strategies' => array(
                'user2note' => 'DoctrineHydrationModule\\Strategy\\ODM\\MongoDB\\ReferencedCollection',
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
        'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
            'input_filter' => 'SecretaryApi\\V1\\Rest\\Note\\Validator',
        ),
        'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
            'input_filter' => 'SecretaryApi\\V1\\Rest\\User2Note\\Validator',
        ),
        'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
            'input_filter' => 'SecretaryApi\\V1\\Rest\\Key\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'SecretaryApi\\V1\\Rest\\Group\\Validator' => array(
            0 => array(
                'name' => 'id',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'ID of group in db - will be set automatically',
            ),
            1 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                        'options' => array(),
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                        'options' => array(),
                    ),
                    2 => array(
                        'name' => 'Zend\\Filter\\StripNewlines',
                        'options' => array(),
                    ),
                ),
                'validators' => array(),
                'description' => 'Name of group',
                'allow_empty' => false,
                'error_message' => 'Please provide a valid "name" value.',
            ),
            2 => array(
                'name' => 'owner',
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\I18n\\Validator\\Int',
                        'options' => array(),
                    ),
                ),
                'description' => 'Owner of a note (int id)',
                'error_message' => 'Please provide a valid "owner" value',
            ),
            3 => array(
                'name' => 'dateCreated',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date group entity was created - will be set automatically',
            ),
            4 => array(
                'name' => 'dateUpdated',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date group entity was updated - will be set automatically',
            ),
            5 => array(
                'name' => 'users',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Users association',
            ),
        ),
        'SecretaryApi\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'name' => 'id',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'User ID inside db - will be given automatically',
            ),
            1 => array(
                'name' => 'username',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Username of user',
            ),
            2 => array(
                'name' => 'email',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Mail address of user',
            ),
            3 => array(
                'name' => 'displayName',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'DisplayName of user',
            ),
            4 => array(
                'name' => 'password',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Password of user, bcrypted',
            ),
            5 => array(
                'name' => 'state',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'State of user',
            ),
            6 => array(
                'name' => 'language',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'User language selection like de_DE or en_US',
            ),
            7 => array(
                'name' => 'notifications',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Enable (true) or disable (false) mail notifications for user',
            ),
            8 => array(
                'name' => 'dateCreated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date user entity was created - will be set automatically',
            ),
            9 => array(
                'name' => 'dateUpdated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date user entity was updated - will be set automatically',
            ),
        ),
        'SecretaryApi\\V1\\Rest\\Note\\Validator' => array(
            0 => array(
                'name' => 'title',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                        'options' => array(),
                    ),
                ),
                'validators' => array(),
                'error_message' => 'Please provide a valid title for the note record.',
                'allow_empty' => false,
                'continue_if_empty' => false,
                'description' => 'Title of the note',
            ),
            1 => array(
                'name' => 'content',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'allow_empty' => false,
                'continue_if_empty' => false,
                'description' => 'Encrypted content of the note',
            ),
            2 => array(
                'name' => 'private',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'allow_empty' => false,
                'continue_if_empty' => false,
                'description' => 'Assigns a note to be private (true) or a group note (false)',
            ),
            3 => array(
                'name' => 'group',
                'required' => false,
                'filters' => array(),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\I18n\\Validator\\Int',
                        'options' => array(),
                    ),
                ),
                'allow_empty' => true,
                'continue_if_empty' => false,
                'description' => 'If note is a group note, this is associated group id',
            ),
            4 => array(
                'name' => 'id',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Note ID inside db - will be given automatically',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            5 => array(
                'name' => 'dateCreated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date note entity was created - will be set automatically',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            6 => array(
                'name' => 'dateUpdated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date note entity was updated - will be set automatically',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
        ),
        'SecretaryApi\\V1\\Rest\\User2Note\\Validator' => array(
            0 => array(
                'name' => 'userId',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'ID of associated user entity',
                'error_message' => 'Please provide a valid "userId" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            1 => array(
                'name' => 'noteId',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'ID of associated note entity',
                'error_message' => 'Please provide a valid "noteId" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            2 => array(
                'name' => 'readPermission',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'If given user has read permission on given note (true/false)',
                'error_message' => 'Please provide a valid "readPermission" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            3 => array(
                'name' => 'writePermission',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'If given user has write permission on given note (true/false)',
                'error_message' => 'Please provide a valid "writePermission" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            4 => array(
                'name' => 'owner',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'If given user is owner of given note (true/false)',
                'error_message' => 'Please provide a valid "owner" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            5 => array(
                'name' => 'eKey',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'eKey for the note content for provided user',
                'error_message' => 'Please provide a valid "eKey" value.',
            ),
            6 => array(
                'name' => 'dateCreated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date user2note entity was created - will be set automatically',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            7 => array(
                'name' => 'dateUpdated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date user2note entity was updated - will be set automatically',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
        ),
        'SecretaryApi\\V1\\Rest\\Key\\Validator' => array(
            0 => array(
                'name' => 'userId',
                'required' => true,
                'filters' => array(),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\I18n\\Validator\\Int',
                        'options' => array(),
                    ),
                ),
                'description' => 'Associated user ID',
                'error_message' => 'Please provide a valid "userID" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            1 => array(
                'name' => 'pubKey',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Public Key of user',
                'error_message' => 'Please provide a valid "pubKey" value.',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
            2 => array(
                'name' => 'dateCreated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date key entity was created - will be set automatically',
            ),
            3 => array(
                'name' => 'dateUpdated',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Date key entity was updated - will be set automatically',
            ),
        ),
    ),
    'zf-mvc-auth' => array(
        'authorization' => array(
            'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
                'entity' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
                'entity' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
                'entity' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => true,
                    'PUT' => false,
                    'DELETE' => true,
                ),
                'collection' => array(
                    'GET' => true,
                    'POST' => true,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
                'entity' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => true,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => true,
                    'POST' => true,
                    'PATCH' => true,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
            'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
                'entity' => array(
                    'GET' => true,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
                'collection' => array(
                    'GET' => false,
                    'POST' => false,
                    'PATCH' => false,
                    'PUT' => false,
                    'DELETE' => false,
                ),
            ),
        ),
    ),
);
