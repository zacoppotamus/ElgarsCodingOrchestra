# Copyright 2013 Michael Jerger
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#    http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#
require "#{File.join(File.dirname(__FILE__),'..','spec_helper.rb')}"

describe 'wget::fetch' do
  let(:title) { 'fetchname1' }
  let(:node) { 'wget.jerger.org' }
  let(:facts) { { :operatingsystem => 'ubuntu' } }
  let(:params) { { :destination => '/dest', :source => 'http://somehost.com/src/' } }
    
  it { should contain_exec('wget-fetchname1').with_command('/usr/bin/wget --user= --output-document=/dest http://somehost.com/src/')
  }
end

describe 'wget::fetch' do
  let(:title) { 'fetchname2' }
  let(:node) { 'wget.jerger.org' }
  let(:facts) { { :operatingsystem => 'ubuntu' } }
  let(:params) { { :destination => '/dest', :source => 'http://somehost.com/src/',
    :no_check_cert => true, :user => 'user2', :password => 'password' } }
    
  it { should contain_exec('wget-fetchname2').with(
    'command' => '/usr/bin/wget --no-check-certificate --user=user2 --output-document=/dest http://somehost.com/src/',
    'environment' => [ "WGETRC=/tmp/wgetrc-fetchname2" ]
  ) }
end
