Installation
============
- You need an nginx/apache setup to run this
- You need a SSL cert for your server/domain to run this!
- You need a MySQL db to run this (using Doctrine ORM - adapter friendly)
- PHP 5.3 with OpenSSL extension (>= 5.3.14)
- Zend Framework 2 + other vendor librarys (coming through composer)

Using Composer to install
-------------------------
1. pull the repository
2. cd into checkout folder
3. php composer.phar install
4. create /config/autoload/local.php from local.php.dist and change the settings to needed values (db, mail, language)
5. ./bin/setup to create tables and load role data
6. create Apache/Nginx host for project with SSL(!)
7. chmod 777 /data/log/app.log
8. chmod -R 777 /data/Secretary/Entity/Proxy if your are using Array Proxy setting

run https://your-secretary.com/user/register and register your first account

Tests
-----
Only a small amount of tests are there right now.

- cd into modules/Secretary/test
- phpunit
