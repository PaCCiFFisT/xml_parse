<?php

namespace App\Extension\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SimpleIdTrait
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }
}
