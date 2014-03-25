# Class: timezone::params
#
# This class defines default parameters used by the main module class timezone
# Operating Systems differences in names and paths are addressed here
#
# == Variables
#
# Refer to timezone class for the variables defined here.
#
# == Usage
#
# This class is not intended to be used directly.
# It may be imported or inherited by other classes
#
class timezone::params {

  $timezone = ''
  $hw_utc = false

  # This is calculated in timezone class
  $set_timezone_command = ''

  $config_file = $::operatingsystem ? {
    /(?i:RedHat|Centos|Scientific|Fedora|Amazon|Linux)/ => '/etc/sysconfig/clock',
    /(?i:Ubuntu|Debian|Mint)/                           => '/etc/timezone',
    /(?i:SLES|OpenSuSE)/                                => '/etc/sysconfig/clock',
    /(?i:FreeBSD|OpenBSD)/                              => '/etc/timezone-puppet',
    /(?i:Solaris)/                                      => '/etc/default/init',
  }

  $config_file_mode = $::operatingsystem ? {
    default => '0644',
  }

  $config_file_owner = $::operatingsystem ? {
    default => 'root',
  }

  $config_file_group = $::operatingsystem ? {
    default => 'root',
  }

  $source = ''
  $template = "timezone/timezone-${::operatingsystem}"
  $options = ''
  $audit_only = false
  $noops = false

}
