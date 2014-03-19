A Puppet module to download files with wget, supporting authentication.

Example

include wget

wget::fetch { "download":
  source => "http://www.google.com/index.html",
  destination => "/tmp/index.html",
}

wget::fetch { "download":
  source => "http://www.google.com/index.html",
  destination => "/tmp/index.html",
  http_user => "user",
  http_password => "password",
  timeout => 0,
}

[![Build Status](https://travis-ci.org/example42/puppet-wget.png?branch=master)](https://travis-ci.org/example42/puppet-wget)
