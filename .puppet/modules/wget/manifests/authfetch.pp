################################################################################
# Definition: wget::authfetch
#
# This class will download files from the internet.  You may define a web proxy
# using $http_proxy if necessary. Username must be provided. And the user's
# password must be stored in the password variable within the .wgetrc file.
#
# Deprecated - use fetch instead
################################################################################
define wget::authfetch(
  $destination,
  $user,
  $bool_no_check_cert = false,
  $password           = '',
  $source             = '',
  $timeout            = '0'
) {
  if $http_proxy {
    $environment = [ "HTTP_PROXY=$http_proxy", "http_proxy=$http_proxy", "WGETRC=/tmp/wgetrc-$name" ]
  }
  else {
    $environment = [ "WGETRC=/tmp/wgetrc-$name" ]
  }
  if $bool_no_check_cert {
    $real_no_check_cert = ' --no-check-certificate'
  }
  else {
    $real_no_check_cert = ''
  }

  file { "/tmp/wgetrc-$name":
    owner   => root,
    mode    => '0600',
    content => "password=$password",
  }

  if isArray($source) {
    exec { $source:
      command     => "/usr/bin/wget --user=$user --output-document=$destination $source$real_no_check_cert",
      timeout     => $timeout,
      unless      => "/usr/bin/test -s $destination",
      environment => $environment,
    }
  }
  else {
    exec { "wget-$name":
      command     => "/usr/bin/wget --user=$user --output-document=$destination $source$real_no_check_cert",
      timeout     => $timeout,
      unless      => "/usr/bin/test -s $destination",
      environment => $environment,
    }
  }
}
