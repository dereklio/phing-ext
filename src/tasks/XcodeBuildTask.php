<?php

require_once('phing/Task.php');

class XcodeBuildTask extends Task {

	private $dir = '';
	private $buildtarget = '';
	private $output = '';
	private $sdk = '';
	private $configuration = '';

	public function setDir($dir) {
		$this->dir = $dir;
	}

	public function setBuildTarget($buildtarget) {
		$this->buildtarget = $buildtarget;
	}

	public function setOutput($output) {
		$this->output = $output;
	}

	public function setSdk($sdk) {
		$this->sdk = $sdk;
	}

	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	public function main() {
		$this->log('Building Xcode Target ' . $this->buildtarget);
		
		$output_dir = realpath($this->output);
		mkdir($output_dir . '/Payload');
		
		$cwd = getcwd();
		$this->log('Switching to ' . $this->dir);
		chdir($this->dir);
		
		$cmd = sprintf('xcodebuild -sdk %s -configuration %s -target %s', $this->sdk, $this->configuration, $this->buildtarget);
		$this->log('Running command: ' . $cmd);
		
		$err = 0;
		system($cmd, $err);
		chdir($cwd);
		if ($err !== 0) throw new BuildException(sprintf('Unable to build %s with Xcode', $this->buildtarget));
		
		$err = 0;
		$cmd = sprintf('cp -R %s/build/%s-%s/%s.app %s', $this->dir, $this->configuration, $this->sdk, $this->buildtarget, $output_dir . '/Payload/');
		system($cmd, $err);
		if ($err !== 0) throw new BuildException(sprintf('Unable to copy the binary to the tmp payload dir %s/Payload', $output_dir));
		
		chdir($output_dir);
		
		$cmd = sprintf('zip --symlinks -r %s.ipa Payload', $this->buildtarget);
		system($cmd, $err);
		system('rm -rf Payload');
		chdir($cwd);
		if ($err !== 0) throw new BuildException(sprintf('Unable to create ipa file', $output_dir));
		
	}

}