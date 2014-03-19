##
# This is a basic Puppet manifest, which we use to install the
# relevant packages.
##

Exec {
    path => ["/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/"]
}

#
# Install Apt and update it with DotDeb and others.
#

include apt
include apt::repo::dotdeb

apt::repository { "dotdeb-php54":
    url => "http://packages.dotdeb.org/",
    distro => "squeeze-php54",
    repository => "all",
    key => "89DF5277"
}

#
# Update some machine settings.
#

exec { "make_group_puppet":
    command => "useradd puppet --no-create-home"
}

class { "timezone":
    timezone => "UTC",
}

exec { "hostname_change":
    command => "hostname rainhawk.dev"
}

#
# Install some native commands we may need.
#

include wget
include git

class { "ntp":
    server => ["0.uk.pool.ntp.org", "1.uk.pool.ntp.org"],
}

#
# Install PHP-FPM.
#

include php::cli
include php::common
include php::fpm::daemon

php::fpm::conf { "www":
    listen  => "127.0.0.1:9000",
    user => "www-data",
    require => Package["nginx"],
    notify => Service["php5-fpm"],
}

php::ini { "/etc/php.ini":
    display_errors => "On",
    memory_limit => "256M",
    short_open_tag => "Off",
    date_timezone => "UTC",
    notify => Service["php5-fpm"],
}

php::ini { "/etc/php5/fpm/php.ini":
    display_errors => "On",
    memory_limit => "256M",
    short_open_tag => "Off",
    date_timezone => "UTC",
    require => Service["php5-fpm"],
}

php::module { ["curl", "gd", "mcrypt", "mongo", "imap", "dev", "xcache", "xdebug"]:
    notify => Service["php5-fpm"],
}

php::module::ini { "xcache":
    settings => {
        "xcache.size" => "256M",
        "xcache.var_size" => "512M",
        "xcache.mmap_path" => "/tmp/xcache",
    },
    notify => Service["php5-fpm"],
}

php::module::ini { "xdebug":
    settings => {
        "xdebug.profiler_enable_trigger" => "1",
    },
    zend => "/usr/lib64/php5/20090626",
    notify => Service["php5-fpm"],
}

#
# Install Nginx.
#

class { "nginx": }

file { "/etc/nginx/conf.d/vhosts.conf":
    owner => root,
    group => root,
    mode => 664,
    source => "/vagrant/.puppet/conf/nginx/vhosts.conf",
    require => Package["nginx"],
    notify => Service["nginx"],
}

#
# Install MongoDB.
#

class {"::mongodb::globals":
    manage_package_repo => true,
}->class {"::mongodb::server":
}

#
# Install Redis for shared caching.
#

class { "redis": }

#
# When we're done, restart all services.
#

exec { "restart_all_services":
    command => "service nginx restart && service php5-fpm restart && service mongodb restart",
    require => [Service["nginx"], Service["php5-fpm"], Service["mongodb"]]
}