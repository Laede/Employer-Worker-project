<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Worker;
use App\Repository\WorkerRepository;

class WorkerService
{
    private $workerRepository;

    public function __construct(WorkerRepository $workerRepository)
    {
        $this->workerRepository = $workerRepository;
    }

    public function getWorker(User $user): Worker
    {
        return $this->workerRepository->findOneBy(['user' => $user]);
    }
    
    public function cvUploaded(User $user): bool
    {
        $worker = $this->workerRepository->findOneBy(['user' => $user]);

        return $worker && $worker->getCv();
    }
}