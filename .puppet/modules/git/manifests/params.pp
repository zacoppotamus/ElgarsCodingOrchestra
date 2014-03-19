# Class: git::params
#
# This class defines default parameters used by the main module class git
# Operating Systems differences in names and paths are addressed here
#
# == Variables
#
# Refer to git class for the variables defined here.
#
# == Usage
#
# This class is not intended to be used directly.
# It may be imported or inherited by other classes
#
class git::params {

  ### Application related parameters

  $package = $::operatingsystem ? {
    default => 'git',
  }

  $config_file = $::operatingsystem ? {
    default => '/etc/gitconfig',
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

  # General Settings
  $my_class = ''
  $source = ''
  $template = ''
  $options = ''
  $version = 'present'
  $absent = false
  $audit_only = false
  $noops = undef

}
