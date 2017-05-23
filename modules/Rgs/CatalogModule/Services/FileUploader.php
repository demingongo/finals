<?php 

namespace Rgs\CatalogModule\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    private $subDir;

    public function __construct($targetDir, $subDir = "")
    {
        $this->targetDir = $targetDir;
        $this->subDir = $subDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getFullPathDir(), $fileName);

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function getSubDir()
    {
        return $this->subDir;
    }

    public function getFullPathDir()
    {
        return !empty($this->subDir) ? $this->targetDir.'/'.$this->subDir : $this->targetDir;
    }
}