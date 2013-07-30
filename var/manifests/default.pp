
exec { 'apt-get-update':
  command => 'apt-get update',
  path    => '/usr/bin/',
  timeout => 60,
  tries   => 3,
}

class { 'apt':
  always_apt_update => false,
}

package { ['python-software-properties']:
  ensure  => 'installed',
  require => Exec['apt-get-update'],
}

file { '/home/vagrant/.bash_aliases':
  ensure => 'present',
  source => 'puppet:///modules/puphpet/dot/.bash_aliases',
}

package { [
    'build-essential',
    'vim',
    'curl'
  ]:
  ensure  => 'installed',
  require => Exec['apt-get-update'],
}

class { 'nginx':
  require => Exec['apt-get-update'],
}


nginx::resource::vhost { 'secretary.dev':
  ensure       => present,
  server_name  => [
    'secretary.dev',
    '33.33.33.155'
  ],
  listen_port  => 80,
  index_files  => [
    'index.html',
    'index.htm',
    'index.php'
  ],
  www_root     => '/var/www/public',
  try_files    => ['$uri', '$uri/', '/index.php?$args'],
}

$path_translated = 'PATH_TRANSLATED $document_root$fastcgi_path_info'
$script_filename = 'SCRIPT_FILENAME $document_root$fastcgi_script_name'

nginx::resource::location { 'secretary.dev-php':
  ensure              => 'present',
  vhost               => 'secretary.dev',
  location            => '~ \.php$',
  proxy               => undef,
  try_files           => ['$uri', '$uri/', '/index.php?$args'],
  www_root            => '/var/www/public',
  location_cfg_append => {
    'fastcgi_split_path_info' => '^(.+\.php)(/.+)$',
    'fastcgi_param'           => 'PATH_INFO $fastcgi_path_info',
    'fastcgi_param '          => $path_translated,
    'fastcgi_param  '         => $script_filename,
    'fastcgi_pass'            => 'unix:/var/run/php5-fpm.sock',
    'fastcgi_index'           => 'index.php',
    'include'                 => 'fastcgi_params'
  },
  notify              => Class['nginx::service'],
}

apt::ppa { 'ppa:ondrej/php5':
  before  => Class['php'],
}

class { 'php':
  package             => 'php5-fpm',
  service             => 'php5-fpm',
  service_autorestart => false,
  config_file         => '/etc/php5/fpm/php.ini',
  module_prefix       => '',
  require             => Exec['apt-get-update'],
}

php::module {
  [
    'php5-cli',
    'php5-curl',
    'php5-intl',
    'php5-mcrypt',
    'php5-mysqlnd',
    'php5-tidy',
    'php-apc',
  ]:
  service => 'php5-fpm',
}

service { 'php5-fpm':
  ensure     => running,
  enable     => true,
  hasrestart => true,
  hasstatus  => true,
  require    => Package['php5-fpm'],
}

class { 'php::devel':
  require => Class['php'],
}

class { 'php::pear':
  require => Class['php'],
}


php::pecl::module { 'APC':
  use_package => true,
}
php::pecl::module { 'PDO':
  use_package => false,
}
php::pecl::module { 'PDO_MYSQL':
  use_package => false,
}

class { 'xdebug':
  service => 'nginx',
}

php::pecl::module { 'xhprof':
  use_package     => false,
  preferred_state => 'beta',
}

nginx::resource::vhost { 'xhprof':
  ensure      => present,
  server_name => ['xhprof'],
  listen_port => 80,
  index_files => ['index.php'],
  www_root    => '/var/www/xhprof/xhprof_html',
  try_files   => ['$uri', '$uri/', '/index.php?$args'],
  require     => Php::Pecl::Module['xhprof']
}


class { 'composer':
  require => Package['php5-fpm', 'curl'],
}

puphpet::ini { 'xdebug':
  value   => [
    'xdebug.default_enable = 1',
    'xdebug.remote_autostart = 0',
    'xdebug.remote_connect_back = 1',
    'xdebug.remote_enable = 1',
    'xdebug.remote_handler = "dbgp"',
    'xdebug.remote_port = 9000'
  ],
  ini     => '/etc/php5/conf.d/zzz_xdebug.ini',
  notify  => Service['php5-fpm'],
  require => Class['php'],
}

puphpet::ini { 'php':
  value   => [
    'date.timezone = "Europe/Berlin"'
  ],
  ini     => '/etc/php5/conf.d/zzz_php.ini',
  notify  => Service['php5-fpm'],
  require => Class['php'],
}

puphpet::ini { 'custom':
  value   => [
    'display_errors = On',
    'error_reporting = -1'
  ],
  ini     => '/etc/php5/conf.d/zzz_custom.ini',
  notify  => Service['php5-fpm'],
  require => Class['php'],
}

class { 'mysql':
      require       => Exec['apt-get-update'],
}

mysql::grant { 'secretary':
  mysql_privileges     => 'ALL',
  mysql_db             => 'secretary',
  mysql_user           => 'secretary',
  mysql_password       => '',
  mysql_host           => 'localhost',
  mysql_grant_filepath => '/home/vagrant/puppet-mysql',
  mysql_create_db      => true,
}

#exec { 'secretary.sql':
#  command => 'mysql -u root -h localhost secretary < /var/www/var/schema/secretary.sql',
#  path    => [ '/usr/bin' , '/usr/sbin' , '/bin' , '/sbin' ],
#  timeout => 60,
#  tries   => 3,
#  require => Class['php','mysql'],
#  creates => "/var/www/var/schema/schema_created",
#  unless  => "ls /var/www/var/schema/schema_created",
#}

exec { 'doctrine schema update':
  command => 'php /var/www/vendor/bin/doctrine-module orm:schema-tool:update --force',
  path    => '/usr/bin/',
  timeout => 60,
  tries   => 3,
  require => Class['php','mysql'],
}

exec { 'mv local conf':
  command => 'cp /var/www/config/autoload/local.php.dist /var/www/config/autoload/local.php',
  path    => [ '/usr/bin' , '/usr/sbin' , '/bin' , '/sbin' ],
  creates => "/var/www/config/autoload/local.php",
  unless  => "ls /var/www/config/autoload/local.php",
}
