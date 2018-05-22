<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Worker;
use App\Repository\WorkerRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CV
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function show($location, $filename = 'CV.pdf')
    {
        header('Content-type:application/pdf');
        header('Content-Disposition:inline;filename="'.$filename.'"');
        readfile($location);
        die;
    }
}