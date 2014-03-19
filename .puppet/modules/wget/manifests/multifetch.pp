# = Define: wget::multifetch
#
# This class will download multiple files from the internet.
#
# == Parameters
#
# [*destination*]
#   The place, where the downloaded result should be stored.
#   This parameter is mandatory.
#
# [*http_proxy*]
#   In order to use a http_proxy this parameter can be used.
#   This parameter is optional, default is undef.
#
# [*http_password*]
#   Http_password is stored securely in the .wgetrc file.
#   This parameter is optional, default is undef.
#
# [*http_user*]
#   The user for the wget download operation.
#   This parameter is optional, default is undef.
#
# [*files*]
#   An array of filenames to download. The given paths are relative to source_base.
#   This parameter is mandatory.
#
# [*no_check_cert*]
#   Wether to check certs in https mode.
#   This parameter is optional, default is flase.
#
# [*source_base*]
#   The Base-URL of the files.
#   This parameter is mandantory.
#
# [*script_user*]
#   The wget executing user.
#   This parameter is optional. Default value is undef.
#
# [*timeout*]
#   How long should be waited for connection & download.
#   This parameter is optional, default is 0.
#
# == Author
#   Michael Jerger <dev@jerger.org/>
#
define wget::multifetch(
  $destination,
  $files,
  $source_base,
  $http_proxy         = undef,
  $http_user          = undef,
  $http_password      = undef,
  $no_check_cert      = false,
  $script_user        = undef,
  $timeout            = '0'
) {
  if $http_proxy {
    $environment = [ "HTTP_PROXY=$http_proxy", "http_proxy=$http_proxy", "WGETRC=/tmp/wgetrc-$name" ]
  }
  elsif $http_password {
    $environment = [ "WGETRC=/tmp/wgetrc-$name" ]
    file { "/tmp/wgetrc-$name":
      before  => Exec[$files],
      content => "password=$http_password",
      mode    => '0600',
      owner   => $script_user,
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

  wget::multifetch::execdefine { $files:
    destination         => $destination,
    environment         => $environment,
    http_user           => $http_user,
    real_no_check_cert  => $real_no_check_cert,
    script_user         => $script_user,
    source_base         => $source_base,
  }
}
