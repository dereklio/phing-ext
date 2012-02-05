<?php

require_once('phing/Task.php');

class MSSQLImportTask extends Task {
	
	private $server = '(localhost)';
	private $username = '';
	private $password = '';
	private $database = '';
	private $trusted = '';
	private $files = array();
	private $sqlcmd = 'sqlcmd';
	
	public function setServer($value) { $this->server = $value; }
	public function setUsername($value) { $this->username = $value; }
	public function setPassword($value) { $this->password = $value; }
	public function setDatabase($value) { $this->database = $value; }
	public function setTrusted($value) { $this->trusted = $value; }
	public function setSqlCmd($value) { $this->sqlcmd = $value; }
	
	public function createFile() {
		$file = new MSSQLImportFile();
		$this->files[] = $file;
		return $file;
	}
	
	public function init() {}
	
	private function importFile($cmd, $file) {
		if (!file_exists($file)) throw new BuildException(sprintf('Unable to open SQL file %s', $file));
		
		$cmd .= ' -i ' . escapeshellarg(realpath($file));
		$this->log(sprintf('Importing %s into %s', $file, $this->database));

		system($cmd, $err);
		if ($err !== 0) throw new BuildException('Unable to import to MS SQL');
	}
	
	public function main() {
		if (empty($this->database)) throw new BuildException('Database name is required for import');
		
		$options = array();
		if (in_array(strtolower($this->trusted), array('yes', 'true'))) $options[] = '-E';
		if (!empty($this->username)) $options = array_merge($options, array('-U', $this->username));
		if (!empty($this->password)) $options = array_merge($options, array('-P', $this->password));
		if (!empty($this->server)) $options = array_merge($options, array('-S', $this->server));
		if (!empty($this->database)) $options = array_merge($options, array('-d', $this->database));
		
		$sqlcmd = escapeshellcmd($this->sqlcmd);
		
		$cmd = implode(' ', array_merge(array($sqlcmd), $options));
		
		foreach ($this->files as $file) $this->importFile($cmd, $file->getName());
	}
	
}

class MSSQLImportFile {
	
	private $name = '';	
	public function setName($value) { $this->name = $value; }
	public function getName() { return $this->name; }
	
}