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
4. php vendor/bin/doctrine-module orm:schema-tool:create --dump-sql
5. load docs/sql/roles.sql into you db 
6. change config/autoload/local.php db values
7. create Apache/Nginx host for project with SSL(!)
8. run https://your-secretery.com/user/register and register your first account