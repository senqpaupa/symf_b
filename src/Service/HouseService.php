<?php

namespace App\Service;

use App\Entity\House;
use Doctrine\ORM\EntityManagerInterface;

class HouseService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createHouse(
        string $name,
        float $pricePerNight,
        int $capacity,
        ?string $description = null
    ): House {
        $house = new House();
        $house->setName($name);
        $house->setPricePerNight($pricePerNight);
        $house->setCapacity($capacity);
        $house->setDescription($description);

        $this->entityManager->persist($house);
        $this->entityManager->flush();

        return $house;
    }

    public function getAllHouses(): array
    {
        return $this->entityManager->getRepository(House::class)
            ->findAll();
    }

    public function getAvailableHouses(\DateTimeInterface $checkIn, \DateTimeInterface $checkOut): array
    {
        $allHouses = $this->getAllHouses();
        $availableHouses = [];

        foreach ($allHouses as $house) {
            if ($this->entityManager->getRepository(House::class)
                ->isHouseAvailable($house, $checkIn, $checkOut)) {
                $availableHouses[] = $house;
            }
        }

        return $availableHouses;
    }

    public function updateHouse(
        House $house,
        ?string $name = null,
        ?float $pricePerNight = null,
        ?int $capacity = null,
        ?string $description = null
    ): House {
        if ($name !== null) {
            $house->setName($name);
        }
        if ($pricePerNight !== null) {
            $house->setPricePerNight($pricePerNight);
        }
        if ($capacity !== null) {
            $house->setCapacity($capacity);
        }
        if ($description !== null) {
            $house->setDescription($description);
        }

        $this->entityManager->flush();

        return $house;
    }

    public function deleteHouse(House $house): void
    {
        $this->entityManager->remove($house);
        $this->entityManager->flush();
    }
} 