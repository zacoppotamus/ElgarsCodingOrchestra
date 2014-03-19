require "#{File.join(File.dirname(__FILE__),'..','spec_helper.rb')}"

describe 'git' do

  let(:title) { 'git' }
  let(:node) { 'rspec.example42.com' }
  let(:facts) { { :ipaddress => '10.42.42.42' } }
  let(:params) { { :template => 'git/spec.erb' } }

  describe 'Test minimal installation' do
    it { should contain_package('git').with_ensure('present') }
    it { should contain_file('git.conf').with_ensure('present') }
  end

  describe 'Test installation of a specific version' do
    let(:params) { {:version => '1.0.42' } }
    it { should contain_package('git').with_ensure('1.0.42') }
  end

  describe 'Test decommissioning - absent' do
    let(:params) { {:absent => true , :template => 'git/spec.erb' } }
    it 'should remove Package[git]' do should contain_package('git').with_ensure('absent') end 
    it 'should remove git configuration file' do should contain_file('git.conf').with_ensure('absent') end
  end

  describe 'Test noops mode' do
    let(:params) { {:noops => true, :template => 'git/spec.erb' } }
    it { should contain_package('git').with_noop('true') }
    it { should contain_file('git.conf').with_noop('true') }
  end

  describe 'Test customizations - template' do
    let(:params) { {:template => "git/spec.erb" , :options => { 'opt_a' => 'value_a' } } }
    it 'should generate a valid template' do
      content = catalogue.resource('file', 'git.conf').send(:parameters)[:content]
      content.should match "fqdn: rspec.example42.com"
    end
    it 'should generate a template that uses custom options' do
      content = catalogue.resource('file', 'git.conf').send(:parameters)[:content]
      content.should match "value_a"
    end
  end

  describe 'Test customizations - source' do
    let(:params) { {:source => "puppet:///modules/git/spec"} }
    it { should contain_file('git.conf').with_source('puppet:///modules/git/spec') }
  end

  describe 'Test customizations - custom class' do
    let(:params) { {:my_class => "git::spec" , :template => 'git/spec.erb' } }
    it { should contain_file('git.conf').with_content(/rspec.example42.com/) }
  end

end
