<?php
return array(
    'SecretaryApi\\V1\\Rest\\Group\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'Get a collection of all groups bound to given identity.',
                'request' => null,
                'response' => '{
    "count": 1,
    "total": 1,
    "collectionTotal": 23,
    "_links": {
        "self": {
            "href": "Link to this group collection"
        },
        "first": {
            "href": "Link to this group collection first page"
        },
        "last": {
            "href": "Link to this group collection last page"
        }
    },
    "_embedded": {
        "group": [
            {
                "id": "ID of group in db - will be set automatically",
                "name": "Name of group",
                "owner": "Owner of a note (int id)",
                "dateCreated": {
                    "date": "2013-02-28 00:30:39",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "dateUpdated": {
                    "date": "2013-03-03 22:52:18",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "users": [
                    1,
                    2,
                    3,
                    4,
                    5
                ],
                "_links" : [
                    "self": {
                        "href": "link to this view"
                    },
                    "notes": {
                        "href": "link to notes view"
                    }
                ]
            }
        ]
    },
    "page_count": 1,
    "page_size": 25,
    "total_items": 1
}',
            ),
            'description' => 'Group collection endpoint',
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Fetch a single group record.',
                'request' => null,
                'response' => '{
    "id": "ID of group in db - will be set automatically",
    "name": "Name of group",
    "owner": "Owner of a note (int id)",
    "dateCreated": {
        "date": "2013-02-28 00:30:39",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2013-03-03 22:52:18",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "users": [
        1,
        2,
        3,
        4,
        5
    ],
    "_links" : [
        "self": {
            "href": "link to this view"
        },
        "notes": {
            "href": "link to notes view"
        }
    ]
}',
            ),
            'description' => 'Group record endpoint',
        ),
        'description' => 'Fetch group collection/record',
    ),
    'SecretaryApi\\V1\\Rest\\User\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'Get a collection of all users of a secretary instance.',
                'request' => null,
                'response' => '{
    "count": 1,
    "total": 1,
    "collectionTotal": 42,
    "_links": {
        "self": {
            "href": "Link to this collection view"
        },
        "first": {
            "href": "Link to this collection view first page"
        },
        "last": {
            "href": "Link to this collection view last page"
        }
    },
    "_embedded": {
        "user": [
            {
                "id": "User ID inside db - will be given automatically",
                "username": "Username of user",
                "email": "Mail address of user",
                "displayName": "DisplayName of user",
                "password": "Password of user, bcrypted",
                "state": "State of user",
                "language": "User language selection like de_DE or en_US",
                "notifications": "Enable (true) or disable (false) mail notifications for user",
                "dateCreated": {
                    "date": "2013-02-28 00:30:23",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "dateUpdated": {
                    "date": "2014-06-30 04:44:31",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "key": null,
                "roles": [
                    4
                ],
                "groups": [
                    2,
                    1
                ],
                "_links": {
                    "self": {
                        "href": "Link to user entity"
                    }
                }
            }
        ]
    },
    "page_count": 1,
    "page_size": 25,
    "total_items": 1
}',
            ),
            'description' => 'User collection endpoint',
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Get user data of given user id.',
                'request' => null,
                'response' => '{
   "id": "User ID inside db - will be given automatically",
   "username": "Username of user",
   "email": "Mail address of user",
   "displayName": "DisplayName of user",
   "password": "Password of user, bcrypted",
   "state": "State of user",
   "language": "User language selection like de_DE or en_US",
   "notifications": "Enable (true) or disable (false) mail notifications for user",
       "dateCreated": {
        "date": "2013-02-28 00:30:23",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-06-30 04:44:31",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "key": null,
    "roles": [
        4
    ],
    "groups": [
        2,
        1
    ],
    "_links": {
        "self": {
            "href": "Link to this entity view"
        }
    }
}',
            ),
            'description' => 'User entity endpoint',
        ),
        'description' => 'Fetch user collection/record',
    ),
    'SecretaryApi\\V1\\Rest\\Note\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'description' => 'Get a paginated collection of note records',
                'request' => null,
                'response' => '{
    "count": 18,
    "total": 18,
    "collectionTotal": 22,
    "_links": {
        "self": {
            "href": "Link to this collection view"
        },
        "first": {
            "href": "Link to this collection view first page"
        },
        "last": {
            "href": "Link to this collection view last page"
        }
    },
    "_embedded": {
        "note": [
            {
                "id": "Note ID inside db - will be given automatically",
                "title": "Title of the note",
                "content": "Encrypted content of the note",
                "private": "Assigns a note to be private (true) or a group note (false)",
                "dateCreated": {
                    "date": "2014-06-30 04:46:50",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "dateUpdated": {
                    "date": "2014-06-30 04:46:50",
                    "timezone_type": 3,
                    "timezone": "Europe/Berlin"
                },
                "groupId": "If note is a group note, this is associated group id",
                "groupName": "If note is a group note, this is associated group name",
                "owner": "If user is owner of note (true) or not (false)",
                "readPermission": "If user has read permission of note (true) or not (false)",
                "writePermission": "If user has write permission of note (true) or not (false)",
                "_embedded": [
                    {
                        "id": 1,
                        "title": "Test 123",
                        "content": "34Z6bq3vlngHc93iSaWKf7UO8olL9vVF",
                        "private": true,
                        "dateCreated": {
                            "date": "2014-06-30 04:46:50",
                            "timezone_type": 3,
                            "timezone": "Europe/Berlin"
                        },
                        "dateUpdated": {
                            "date": "2014-06-30 04:46:50",
                            "timezone_type": 3,
                            "timezone": "Europe/Berlin"
                        },
                        "group": null,
                        "_links": {
                            "self": {
                                "href": "Link to note entity"
                            }
                        }
                    }
                ],
                "_links": {
                    "self": {
                        "href": "Link to note entity"
                    }
                }
            },
        ]
    },
    "page_count": 1,
    "page_size": 1,
    "total_items": 1
}',
            ),
            'POST' => array(
                'description' => 'Create a new note with already encrypted content by client',
                'request' => '{
   "title": "Title of the note",
   "content": "Encrypted content of the note",
   "private": "Assigns a note to be private (true) or a group note (false)",
   "group": "If note is a group note, this is associated group id"
}',
                'response' => '{
    "id": "Note ID inside db - will be given automatically",,
    "title": "Title of the note",
    "content": "Encrypted content of the note",
    "private": "Assigns a note to be private (true) or a group note (false)",
    "dateCreated": {
        "date": "2014-07-08 01:01:53",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-07-08 01:01:53",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "_embedded": {
        "group": { 
        }
    },
    "_links": {
        "self": {
            "href": "Link to note view"
        }
    }
}',
            ),
            'description' => 'Note Collection endpoint',
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Fetch a single note record',
                'request' => null,
                'response' => '{
    "id": "Note ID inside db - will be given automatically",
    "title": "Title of the note",
    "content": "Encrypted content of the note",
    "private": "Assigns a note to be private (true) or a group note (false)",
    "dateCreated": {
        "date": "2014-06-30 04:50:11",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-06-30 04:50:11",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "owner": "If user is owner of note (true) or not (false)",
    "readPermission": "If user has read permission of note (true) or not (false)",
    "writePermission": "If user has write permission of note (true) or not (false)",
    "eKey": "eKey for encryption of content for user",
    "groupId": "If note is not private this is associated Group ID is",
    "groupName": "If note is not private this is associated Group name",
    "_links": {
        "self": {
            "href": "Link to this view"
        }
    }
}',
            ),
            'description' => 'Note Record endpoint',
        ),
        'description' => 'Note Service - create and read/list notes out of secretary',
    ),
    'SecretaryApi\\V1\\Rest\\User2Note\\Controller' => array(
        'collection' => array(
            'GET' => array(
                'request' => null,
                'response' => null,
            ),
            'POST' => array(
                'description' => 'Create a user2note record',
                'request' => '{
   "userId": "ID of associated user entity",
   "noteId": "ID of associated note entity",
   "readPermission": "If given user has read permission on given note (true/false)",
   "writePermission": "If given user has write permission on given note (true/false)",
   "owner": "If given user is owner of given note (true/false)",
   "eKey": "eKey for the note content for provided user"
}',
                'response' => '{
    "userId": "ID of associated user entity",
    "noteId": "ID of associated note entity",
    "eKey": "eKey for the note content for provided user"
    "readPermission": "If given user has read permission on given note (true/false)",
    "writePermission": "If given user has write permission on given note (true/false)",
    "owner": "If given user is owner of given note (true/false)",
    "dateCreated": {
        "date": "2014-07-08 01:40:35",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-07-08 01:40:35",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "_embedded": {
        "user": {},
        "note": {}
    },
    "_links": {
        "self": {
            "href": "Link to user2note entity view"
        }
    }
}',
            ),
            'description' => 'User2Note Collection endpoint',
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Get a user2note record',
                'request' => null,
                'response' => '{
    "userId": "ID of associated user entity",
    "noteId": "ID of associated note entity",
    "eKey": "eKey for the note content for provided user"
    "readPermission": "If given user has read permission on given note (true/false)",
    "writePermission": "If given user has write permission on given note (true/false)",
    "owner": "If given user is owner of given note (true/false)",
    "dateCreated": {
        "date": "2014-07-08 01:40:35",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-07-08 01:40:35",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "_embedded": {
        "user": {},
        "note": {}
    },
    "_links": {
        "self": {
            "href": "Link to user2note entity view"
        }
    }
}',
            ),
            'description' => 'User2Note entity endpoint',
        ),
        'description' => 'User2Note Service - create and read/list related user2notes entities of secretary',
    ),
    'SecretaryApi\\V1\\Rest\\Key\\Controller' => array(
        'collection' => array(
            'description' => 'Key Collection endpoint',
        ),
        'entity' => array(
            'GET' => array(
                'description' => 'Get a key record for provided user ID',
                'request' => null,
                'response' => '{
    "userId": "Associated user ID",
    "pubKey": "Public Key of user",
    "dateCreated": {
        "date": "2014-06-30 04:44:31",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "dateUpdated": {
        "date": "2014-06-30 04:44:31",
        "timezone_type": 3,
        "timezone": "Europe/Berlin"
    },
    "_embedded": {
        "user": {}
    },
    "_links": {
        "self": {
            "href": "Link to this key record view"
        }
    }
}',
            ),
            'description' => 'Key Entity endpoint',
        ),
        'description' => 'Key Service - fetch user associated key entities of secretary',
    ),
);
