<?php

namespace App\Service;


use App\Entity\Apply;
use App\Entity\Messages;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MessagesService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveMessage(Messages $message,User $user, Apply $apply)
    {
        $message->setUser($user);
        $message->setTimestamp(new \DateTime());
        $message->setApply($apply);
        $this->em->persist($message);
        $this->em->flush();
    }
}