<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;

readonly class CrudManager
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function create(object $entity): object
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function update(object $entity): object
    {
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
