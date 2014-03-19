# = Class: git
#
# This is the main git class
#
#
# == Parameters
#
# Standard class parameters
# Define the general class behaviour and customizations
#
# [*my_class*]
#   Name of a custom class to autoload to manage module's customizations
#   If defined, git class will automatically "include $my_class"
#   Can be defined also by the (top scope) variable $git_myclass
#
# [*source*]
#   Sets the content of source parameter for main configuration file
#   If defined, git main config file will have the param: source => $source
#   Can be defined also by the (top scope) variable $git_source
#
# [*template*]
#   Sets the path to the template to use as content for main configuration file
#   If defined, git main config file has: content => content("$template")
#   Note source and template parameters are mutually exclusive: don't use both
#   Can be defined also by the (top scope) variable $git_template
#
# [*options*]
#   An hash of custom options to be used in templates for arbitrary settings.
#   Can be defined also by the (top scope) variable $git_options
#
# [*version*]
#   The package version, used in the ensure parameter of package type.
#   Default: present. Can be 'latest' or a specific version number.
#   Note that if the argument absent (see below) is set to true, the
#   package is removed, whatever the value of version parameter.
#
# [*absent*]
#   Set to 'true' to remove package(s) installed by module
#   Can be defined also by the (top scope) variable $git_absent
#
# [*audit_only*]
#   Set to 'true' if you don't intend to override existing configuration files
#   and want to audit the difference between existing files and the ones
#   managed by Puppet.
#   Can be defined also by the (top scope) variables $git_audit_only
#   and $audit_only
#
# [*noops*]
#   Set noop metaparameter to true for all the resources managed by the module.
#   Basically you can run a dryrun for this specific module if you set
#   this to true. Default: undef
#
# Default class params - As defined in git::params.
# Note that these variables are mostly defined and used in the module itself,
# overriding the default values might not affected all the involved components.
# Set and override them only if you know what you're doing.
# Note also that you can't override/set them via top scope variables.
#
# [*package*]
#   The name of git package
#
# [*config_file*]
#   Main configuration file path
#
# == Examples
#
# You can use this class in 2 ways:
# - Set variables (at top scope level on in a ENC) and "include git"
# - Call git as a parametrized class
#
# See README for details.
#
#
class git (
  $my_class            = params_lookup( 'my_class' ),
  $source              = params_lookup( 'source' ),
  $template            = params_lookup( 'template' ),
  $options             = params_lookup( 'options' ),
  $version             = params_lookup( 'version' ),
  $absent              = params_lookup( 'absent' ),
  $audit_only          = params_lookup( 'audit_only' , 'global' ),
  $noops               = params_lookup( 'noops' ),
  $package             = params_lookup( 'package' ),
  $config_file         = params_lookup( 'config_file' )
  ) inherits git::params {

  $config_file_mode=$git::params::config_file_mode
  $config_file_owner=$git::params::config_file_owner
  $config_file_group=$git::params::config_file_group

  $bool_absent=any2bool($absent)
  $bool_audit_only=any2bool($audit_only)

  ### Definition of some variables used in the module
  $manage_package = $git::bool_absent ? {
    true  => 'absent',
    false => $git::version,
  }

  $manage_file = $git::bool_absent ? {
    true    => 'absent',
    default => 'present',
  }

  $manage_audit = $git::bool_audit_only ? {
    true  => 'all',
    false => undef,
  }

  $manage_file_replace = $git::bool_audit_only ? {
    true  => false,
    false => true,
  }

  $manage_file_source = $git::source ? {
    ''        => undef,
    default   => $git::source,
  }

  $manage_file_content = $git::template ? {
    ''        => undef,
    default   => template($git::template),
  }

  ### Managed resources
  if !defined(Package[$git::package]) {
    package { $git::package:
      ensure  => $git::manage_package,
      noop    => $git::noops,
    }
  }

  if $git::source != ''
  or $git::template != '' {
    file { 'git.conf':
      ensure  => $git::manage_file,
      path    => $git::config_file,
      mode    => $git::config_file_mode,
      owner   => $git::config_file_owner,
      group   => $git::config_file_group,
      require => Package[$git::package],
      source  => $git::manage_file_source,
      content => $git::manage_file_content,
      replace => $git::manage_file_replace,
      audit   => $git::manage_audit,
      noop    => $git::noops,
    }
  }

  ### Include custom class if $my_class is set
  if $git::my_class {
    include $git::my_class
  }

}
