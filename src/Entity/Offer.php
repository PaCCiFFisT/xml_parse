<?php

namespace App\Entity;

use App\Extension\Entity\Traits\SimpleIdTrait;
use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: OfferRepository::class)]
#[Table(name: 'offers')]
class Offer
{
    use SimpleIdTrait;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $vendor;

    #[Column(name: 'categoryId', type: 'string', length: 255, nullable: true)]
    private ?string $categoryId;

    #[Column(name: 'vendorCode', type: 'string', length: 255, nullable: true)]
    private ?string $vendorCode;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    #[Column(type: 'string', length: 255, scale: 2, nullable: true)]
    private ?string $price;

    #[Column(type: 'string', length: 255, nullable: true)]
    private ?string $barcode = null;

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(?string $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getVendorCode(): ?string
    {
        return $this->vendorCode;
    }

    public function setVendorCode(?string $vendorCode): void
    {
        $this->vendorCode = $vendorCode;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): void
    {
        $this->price = $price;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): void
    {
        $this->barcode = $barcode;
    }
}