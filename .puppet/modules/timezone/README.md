# Puppet module: timezone

This is a Puppet module for timezone management, based on the second generation layout ("NextGen") of Example42 Puppet Modules.

Made by Alessandro Franceschi / Lab42

Official site: http://www.example42.com

Official git repository: http://github.com/example42/puppet-timezone

Released under the terms of Apache 2 License.

This module requires functions provided by the Example42 Puppi module (you need it even if you don't use and install Puppi)

For detailed info about the logic and usage patterns of Example42 modules check the DOCS directory on Example42 main modules set.

## USAGE 

* Set the desired timezone 

        class { 'timezone':
          timezone => 'Europe/Rome',
        }

Alternatively you can define the ::timezone top scope variable somewhere (in an ENC) and just:

        $::timezone = 'Europe/Rome'
        include timezone

* Specify if hardware clock uses GMT (default is 'false' and localtime is used)

        class { 'timezone':
          timezone => 'Europe/Rome',
          hw_utc   => true,
        }

  or:

        $::timezone = 'Europe/Rome'
        $::timezone_hw_utc = true
        include timezone


* Use custom template for timezone config file. Refer to $config_file in params.pp to see what's actually the file managed.
  Note that template and source arguments are alternative. 

        class { 'timezone':
          template => 'example42/timezone/timezone.conf.erb',
        }


* Automatically include a custom subclass

        class { 'timezone':
          my_class => 'example42::my_timezone',
        }



[![Build Status](https://travis-ci.org/example42/puppet-timezone.png?branch=master)](https://travis-ci.org/example42/puppet-timezone)
