<?php

require_once('phing/Task.php');

class TestflightTask extends Task {

	private $file = '';
	private $api_token = '';
	private $team_token = '';
	private $notes = '';
	private $notify = '';
	private $distribution_lists = '';
	private $replace = '';

	public function setFile($file) {
		$this->file = $file;
	}
	
	public function setApiToken($api_token) {
		$this->api_token = $api_token;
	}
	
	public function setTeamToken($team_token) {
		$this->team_token = $team_token;
	}
	
	public function setNotes($notes) {
		$this->notes = $notes;
	}
	
	public function setNotify($notify) {
		$this->notify = $notify ? 'True' : 'False';
	}
	
	public function setDistributionLists($distribution_lists) {
		$this->distribution_lists = $distribution_lists;
	}

	public function setReplace($replace) {
		$this->replace = $replace ? 'True' : 'False';
	}
	
	private function getCurlOption($upload) {
		switch ($upload) {
			case 2: return "-F %s='@%s'";
			case 1: return "-F %s='<%s'";
			default: return "-F %s='%s'";
		}
	}

	public function main() {
		$options = array();
		foreach (array(
			'file' => 2,
			'api_token' => 0,
			'team_token' => 0,
			'notes' => 1,
			'notify' => 0,
			'distribution_lists' => 0,
			'replace' => 0,
		) as $key => $upload) {
			$options[] = sprintf($this->getCurlOption($upload), $key, $this->{$key});
		}
		$cmd = sprintf('curl http://testflightapp.com/api/builds.json %s', implode(' ', $options));
		$this->log('Running command: ' . $cmd);
		
		$err = 0;
		system($cmd, $err);
		if ($err !== 0) throw new BuildException(sprintf('Unable to upload %s to Testflight', $this->file));
	}

}