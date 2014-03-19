# = Define: wget::fetch
#
# This class will download one file from the internet.
#
# == Parameters
#
# [*destination*]
#   The place, where the downloaded result should be stored.
#   This parameter is mandantory.
#
# [*http_proxy*]
#   In order to use a http_proxy this parameter can be used.
#   This parameter is optional, default is undef.
#
# [*http_user*]
#   The user for the wget download operaton.
#   This parameter is optional, default is undef.
#
# [*http_password*]
#   Password is stored securely in the .wgetrc file.
#   This parameter is optional, default is undef.
#
# [*no_check_cert*]
#   Wether to check certs in https mode.
#   This parameter is optional, default is flase.
#
# [*password*]
#   Password is stored securely in the .wgetrc file.
#   This parameter is deprecated (use http_password instead) & optional, default is undef.
#
# [*source*]
#   The URL of the file.
#   This parameter is mandantory.
#
# [*timeout*]
#   How long should be waited for connection & download.
#   This parameter is optional, default is 0.
#
# [*user*]
#   The user for the wget download operaton.
#   This parameter is deprecated (use http_user instead) & optional, default is undef.
#
# == Author
#   Michael Jerger <dev@jerger.org/>
#
define wget::fetch(
  $source,
  $destination,
  $http_password      = undef,
  $http_proxy         = undef,
  $http_user          = undef,
  $no_check_cert      = false,
  $password           = undef,
  $timeout            = '0',
  $user               = undef
) {
  $managed_http_password = $http_password ? {
    undef   => $password,
    default => http_password,
  }
  $managed_http_user = $http_user ? {
    undef   => $user,
    default => http_user,
  }

  if $http_proxy {
    $environment = [ "HTTP_PROXY=$http_proxy", "http_proxy=$http_proxy", "WGETRC=/tmp/wgetrc-$name" ]
  }
  elsif $managed_http_password {
    $environment = [ "WGETRC=/tmp/wgetrc-$name" ]
    file { "/tmp/wgetrc-$name":
      before  => Exec[$source],
      content => "password=$managed_http_password",
      mode    => '0600',
      owner   => root,
    }
  } else {
    $environment = []
  }

  if $no_check_cert {
    $real_no_check_cert = '--no-check-certificate '
  }
  else {
    $real_no_check_cert = ''
  }

  exec { "wget-$name":
    command     => "/usr/bin/wget $real_no_check_cert--user=$managed_http_user --output-document=$destination $source",
    timeout     => $timeout,
    unless      => "/usr/bin/test -s $destination",
    environment => $environment,
  }
}
