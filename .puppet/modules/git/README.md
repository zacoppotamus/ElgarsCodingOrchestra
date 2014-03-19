# Puppet module: git

This is a Puppet module for git
It provides only package installation and file configuration.

Based on Example42 layouts by Alessandro Franceschi / Lab42

Official site: http://www.example42.com

Official git repository: http://github.com/example42/puppet-git

Released under the terms of Apache 2 License.

This module requires the presence of Example42 Puppi module in your modulepath.


## USAGE - Basic management

* Install git with default settings

        class { 'git': }

* Install a specific version of git package

        class { 'git':
          version => '1.0.1',
        }

* Remove git resources

        class { 'git':
          absent => true
        }

* Enable auditing without making changes on existing git configuration *files*

        class { 'git':
          audit_only => true
        }

* Module dry-run: Do not make any change on *all* the resources provided by the module

        class { 'git':
          noops => true
        }


## USAGE - Overrides and Customizations
* Use custom sources for main config file

        class { 'git':
          source => [ "puppet:///modules/example42/git/git.conf-${hostname}" , "puppet:///modules/example42/git/git.conf" ], 
        }

* Use custom template for main config file. Note that template and source arguments are alternative.

        class { 'git':
          template => 'example42/git/git.conf.erb',
        }

* Automatically include a custom subclass

        class { 'git':
          my_class => 'example42::my_git',
        }


## CONTINUOUS TESTING

Travis {<img src="https://travis-ci.org/example42/puppet-git.png?branch=master" alt="Build Status" />}[https://travis-ci.org/example42/puppet-git]
