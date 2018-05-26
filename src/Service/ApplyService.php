<?php
/**
 * Created by PhpStorm.
 * User: darius
 * Date: 18.5.26
 * Time: 15.21
 */

namespace App\Service;


use App\Entity\Apply;
use Doctrine\ORM\EntityManagerInterface;

class ApplyService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function changeStatus(Apply $apply, $status)
    {
        $apply->setStatus($status?'accepted':'declined');
        $this->em->flush();
    }

    public function groupApplies($applies)
    {
        $grouped = [
            'new'       => [],
            'accepted'  => [],
            'declined'  => [],
        ];
        /** @var Apply $apply */
        foreach($applies as $apply) {
            $grouped[$apply->getStatus()][] = $apply;
        }
        return $grouped;
    }
}