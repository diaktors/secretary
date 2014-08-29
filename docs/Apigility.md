Use Apigility
=============
To run the Apigility API of secretary, you need to provide an `users.htpasswd` file inside `data` folder.
For now it's necessary to provide a list of users, that should be able to use the
(SecretaryClient)[https://github.com/wesrc/secretaryclient] tool or some other client tool.

You need to use the users email address as username, he is using inside secretary.

Apigility provides an OAuth adapter, so for future usage it could be possible to replace htpasswd with that or
an own secretary adapter for that.


Apigility UI
------------
Open `http://secretary.domain/apigility/ui` to view apigility ui. You need to be admin for that.

Apigility Documentation
-----------------------
Open `http://secretary.domain/apigility/documentation` to view documentation of API through apigility swagger module.
You need to be admin for that.