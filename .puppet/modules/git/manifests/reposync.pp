# = Define: git::reposync
#
# This define creates, executes and optionally crontabs a
# simple git_reposync_* script that exports or checkouts a git
# repository to a local directory.
# By default this script is placed in:
# /usr/local/sbin/git_reposync_${name}
# and can be executed directly by hand, via Puppet (if autorun is true)
# via cron (if cron is defined) or via the master script:
# /usr/local/sbin/git_reposync
#
# == Parameters:
#
# [*source_url*]
#   Url of the repository to use. As passed to the git command
#   present in the git_reposync script. Required.
#
# [*destination_dir*]
#   Local directory where to sync the repository, As passed to the
#   git command present in the git_reposync script. Required.
#
# [*extra_options*]
#   Optional extra options to add to git command. Default: ''.
#
# [*branch*]
#   Optional branch name defaults to master
#
# [*autorun*]
#   Define if to automatically execute the git_reposync script when
#   Puppet runs. Default: true.
#
# [*creates*]
#   Path of a file or directory created by the git command. If it
#   exists Puppet does not automatically execute the git_reposync
#   command (when autorun is enabled). Default: $destination_dir.
#
# [*pre_command*]
#   Optional comman to execute before executing the git command.
#   Note that this command is placed in the git_reposync script created
#   by this define and it's executed every time this script is run (either
#   manually or via Puppet). Default: ''
#
# [*post_command*]
#   Optional comman to execute after executing the git command.
#   Note that this command is placed in the git_reposync script created
#   by this define and it's executed every time this script is run (either
#   manually or via Puppet). Default: ''
#
# [*basedir*]
#   Directory where the git_reposync scripts are created.
#   Default: /usr/local/sbin
#
# [*cron*]
#   Optional cron schedule to crontab the execution of the
#   git_reposync script. Format must be in standard cron style.
#   Example: '0 4 * * *' . Default: '' (no cron scheduled).
#
# [*owner*]
#   Owner of the created git_reposync script. Default: root.
#
# [*group*]
#   Group of the created git_reposync script. Default: root.
#
# [*mode*]
#   Mode of the created git_reposync script. Default: '7550'.
#   NOTE: Keep the execution flag!
#
# [*ensure*]
#   Define if the git_reposync script and eventual cron job
#   must be present or absent. Default: present.
#
# == Examples
#
# - Minimal setup (with autorun and export)
# git::reposync { 'my_app':
#   source_url      => 'http://repo.example42.com/git/trunk/my_app/',
#   destination_dir => '/opt/myapp',
# }
#
# - Execute a custom command after git (with default autorun)
# git::reposync { 'my_app':
#   source_url      => 'http://repo.example42.com/git/trunk/my_app/',
#   destination_dir => '/opt/myapp',
#   post_command    => 'chown -R my_user:my_user /opt/myapp',
# }
#
define git::reposync (
  $source_url,
  $destination_dir,
  $extra_options   = '',
  $branch          = 'master',
  $autorun         = true,
  $creates         = $destination_dir,
  $pre_command     = '',
  $post_command    = '',
  $basedir         = '/usr/local/sbin',
  $cron            = '',
  $owner           = 'root',
  $group           = 'root',
  $mode            = '0755',
  $ensure          = 'present' ) {

  include git

  if ! defined(File['git_reposync']) {
    file { 'git_reposync':
      ensure  => present,
      path    => "${basedir}/git_reposync",
      mode    => $mode,
      owner   => $owner,
      group   => $group,
      content => template('git/reposync/git_reposync.erb'),
    }
  }

  file { "git_reposync_${name}":
    ensure  => $ensure,
    path    => "${basedir}/git_reposync_${name}",
    mode    => $mode,
    owner   => $owner,
    group   => $group,
    content => template('git/reposync/git_reposync-command.erb'),
    require => Package[$git::package],
  }

  if $autorun == true {
    exec { "git_reposync_run_${name}":
      command     => "${basedir}/git_reposync_${name}",
      creates     => $creates,
    }
  }

  if $cron != '' {
    file { "git_reposync_cron_${name}":
      ensure  => $ensure,
      path    => "/etc/cron.d/git_reposync_${name}",
      mode    => '0644',
      owner   => 'root',
      group   => 'root',
      content => template('git/reposync/git_reposync-cron.erb'),
    }
  }
}
