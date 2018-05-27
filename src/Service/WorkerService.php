<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Worker;
use App\Repository\WorkerRepository;
use Symfony\Component\HttpFoundation\Request;

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

    public function proceedFilters(Request $request, User $user)
    {
        $filter2method = [
            'skills'        => 'filterSkills',
            'my_skills'     => 'filterSkills',
            'budget_from'   => 'filterBudgetFrom',
            'budget_to'     => 'filterBudgetTo'
        ];
        $filters = [];
        foreach($request->query as $key => $item) {
            if($item && isset($filter2method[$key])) {
                $filters[$key] = [
                    'filter'    => $key,
                    'method'    => $filter2method[$key],
                    'value'     => $key === 'my_skills'?$this->getWorker($user)->getSkills():$item,
                ];
            }
        }
        return $filters;
    }
}