define wget::multifetch::execdefine(
  $destination,
  $environment,
  $http_user,
  $real_no_check_cert,
  $script_user,
  $source_base
) {
  $filename = url_parse("$source_base/$title", filename)

  exec { $title:
    command     => "/usr/bin/wget $real_no_check_cert--user=$http_user --output-document=$destination/$filename $source_base/$title",
    timeout     => $timeout,
    unless      => "/usr/bin/test -s $destination/$filename",
    user        => $script_user,
    environment => $environment,
  }
}
