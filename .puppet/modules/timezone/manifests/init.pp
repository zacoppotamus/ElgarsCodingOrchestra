# Class timezone
#
# This class managed the System's timezone
#
# Parameters:
#
# [*timezone*]
#   The timezone to use
#
# [*hw_utc*]
#   If system clock is set to UTC. Default: false
#
# [*set_timezone_command*]
#   The command to execute to apply the new timezone.
#   Default is automatically set according to OS
#
# [*my_class*]
#   Name of a custom class to autoload to manage module's customizations
#   If defined, timezone class will automatically "include $my_class"
#   Can be defined also by the (top scope) variable $timezone_myclass
#
# [*source*]
#   Sets the content of source parameter for main configuration file
#   If defined, timezone main config file will have the param: source => $source
#   Can be defined also by the (top scope) variable $timezone_source
#
# [*template*]
#   Sets the path to the template to use as content for main configuration file
#   If defined, timezone main config file has: content => content("$template")
#   Note source and template parameter s are mutually exclusive: don't use both
#   Can be defined also by the (top scope) variable $timezone_template
#
# [*options*]
#   An hash of custom options to be used in templates for arbitrary settings.
#   Can be defined also by the (top scope) variable $timezone_options
#
# [*audit_only*]
#   Set to 'true' if you don't intend to override existing configuration files
#   and want to audit the difference between existing files and the ones
#   managed by Puppet.
#
# [*noops*]
#   Set noop metaparameter to true for all the resources managed by the module.
#   Basically you can run a dryrun for this specific module if you set
#   this to true. Default: false
#
# [*config_file*]
#   Main configuration file path
#
# [*config_file_mode*]
#   Main configuration file path mode
#
# [*config_file_owner*]
#   Main configuration file path owner
#
# [*config_file_group*]
#   Main configuration file path group
#
class timezone(
  $timezone             = params_lookup( 'timezone', 'global' ),
  $hw_utc               = params_lookup( 'hw_utc' ),
  $set_timezone_command = params_lookup( 'set_timezone_command' ),
  $my_class             = params_lookup( 'my_class' ),
  $source               = params_lookup( 'source' ),
  $template             = params_lookup( 'template' ),
  $options              = params_lookup( 'options' ),
  $audit_only           = params_lookup( 'audit_only' , 'global' ),
  $noops                = params_lookup( 'noops' ),
  $config_file          = params_lookup( 'config_file' ),
  $config_file_mode     = params_lookup( 'config_file_mode' ),
  $config_file_owner    = params_lookup( 'config_file_owner' ),
  $config_file_group    = params_lookup( 'config_file_group' )
  ) inherits timezone::params {

  $bool_audit_only=any2bool($audit_only)
  $bool_noops=any2bool($noops)

  $real_set_timezone_command = $set_timezone_command ? {
    ''      => $::operatingsystem ? {
      /(?i:RedHat|Centos|Scientific|Fedora|Amazon|Linux)/ => 'tzdata-update',
      /(?i:Ubuntu|Debian|Mint)/                           => 'dpkg-reconfigure -f noninteractive tzdata',
      /(?i:SLES|OpenSuSE)/                                => "zic -l ${timezone}",
      /(?i:OpenBSD)/                                      => "ln -fs /usr/share/zoneinfo/${timezone} /etc/localtime",
      /(?i:FreeBSD)/                                      => "cp /usr/share/zoneinfo/${timezone} /etc/localtime && adjkerntz -a",
      /(?i:Solaris)/                                      => "rtc -z ${timezone} && rtc -c",
    },
    default => $set_timezone_command,
  }

  $manage_audit = $timezone::bool_audit_only ? {
    true  => 'all',
    false => undef,
  }

  $manage_file_replace = $timezone::bool_audit_only ? {
    true  => false,
    false => true,
  }

  $manage_file_source = $timezone::source ? {
    ''        => undef,
    default   => $timezone::source,
  }

  $manage_file_content = $timezone::template ? {
    ''        => undef,
    default   => template($timezone::template),
  }

  file { 'timezone':
    ensure  => present,
    path    => $timezone::config_file,
    mode    => $timezone::config_file_mode,
    owner   => $timezone::config_file_owner,
    group   => $timezone::config_file_group,
    source  => $timezone::manage_file_source,
    content => $timezone::manage_file_content,
    replace => $timezone::manage_file_replace,
    audit   => $timezone::manage_audit,
    noop    => $timezone::bool_noops,
  }

  if $::hardwareisa != 'sparc' and $::kernel != 'SunOS' {
    exec { 'set-timezone':
      command     => $timezone::real_set_timezone_command,
      path        => '/usr/bin:/usr/sbin:/bin:/sbin',
      require     => File['timezone'],
      subscribe   => File['timezone'],
      refreshonly => true,
    }
  }

}
