<?php

require_once('phing/Task.php');
include_once('phing/types/FileSet.php');

class CsstidyTask extends Task {

    private $filesets = array();

	private $options = '';

	public function setOptions($options) {
		$this->options = $options;
	}

	public function main() {
		$options = array();
		foreach (explode(' ', $this->options) as $option) {
			$options[] = escapeshellarg($option);
		}

        foreach($this->filesets as $fs) {
			$ds = $fs->getDirectoryScanner($this->project);
			$from_dir = $fs->getDir($this->project);
			$css_files = $ds->getIncludedFiles();

			foreach ($css_files as $css_file) {
				$file = new PhingFile($from_dir, $css_file);

				$cmd = sprintf('csstidy %1$s %2$s %1$s', $file->__toString(), implode(' ', $options));
				$this->log('Running ' . $cmd);
				if ($file->exists()) {
					$err = 0;
					system($cmd, $err);
					if ($err !== 0) throw new BuildException(sprintf('Unable to process %s with csstidy', $file->__toString()));
				} else {
					throw new BuildException(sprintf('The file %s does not exist', $file->__toString()));
				}
			}
        }
	}

    public function createFileSet() {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num - 1];
    }

}