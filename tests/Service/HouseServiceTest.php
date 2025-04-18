<?php

namespace App\Tests\Service;

use App\Entity\House;
use App\Service\HouseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HouseServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?HouseService $houseService = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
            
        $this->houseService = new HouseService($this->entityManager);
    }

    public function testCreateHouse(): void
    {
        // Создаем новый домик
        $house = $this->houseService->createHouse(
            'Тестовый домик',
            100.0,
            4,
            'Описание тестового домика'
        );

        // Проверяем, что домик создан с правильными данными
        $this->assertNotNull($house->getId());
        $this->assertEquals('Тестовый домик', $house->getName());
        $this->assertEquals(100.0, $house->getPricePerNight());
        $this->assertEquals(4, $house->getCapacity());
        $this->assertEquals('Описание тестового домика', $house->getDescription());

        // Проверяем, что домик сохранен в базе
        $savedHouse = $this->entityManager->getRepository(House::class)->find($house->getId());
        $this->assertNotNull($savedHouse);
        $this->assertEquals($house->getName(), $savedHouse->getName());
    }

    public function testGetAllHouses(): void
    {
        // Создаем несколько тестовых домиков
        $house1 = $this->houseService->createHouse('Домик 1', 100.0, 2);
        $house2 = $this->houseService->createHouse('Домик 2', 150.0, 4);
        $house3 = $this->houseService->createHouse('Домик 3', 200.0, 6);

        // Получаем все домики
        $houses = $this->houseService->getAllHouses();

        // Проверяем, что все домики получены
        $this->assertCount(3, $houses);
        $this->assertContainsEquals($house1, $houses);
        $this->assertContainsEquals($house2, $houses);
        $this->assertContainsEquals($house3, $houses);
    }

    public function testUpdateHouse(): void
    {
        // Создаем тестовый домик
        $house = $this->houseService->createHouse('Старое название', 100.0, 4);
        $originalId = $house->getId();

        // Обновляем данные домика
        $updatedHouse = $this->houseService->updateHouse(
            $house,
            'Новое название',
            150.0,
            6,
            'Новое описание'
        );

        // Проверяем, что данные обновились
        $this->assertEquals($originalId, $updatedHouse->getId());
        $this->assertEquals('Новое название', $updatedHouse->getName());
        $this->assertEquals(150.0, $updatedHouse->getPricePerNight());
        $this->assertEquals(6, $updatedHouse->getCapacity());
        $this->assertEquals('Новое описание', $updatedHouse->getDescription());

        // Проверяем, что данные обновились в базе
        $this->entityManager->clear();
        $savedHouse = $this->entityManager->getRepository(House::class)->find($originalId);
        $this->assertEquals('Новое название', $savedHouse->getName());
    }

    public function testDeleteHouse(): void
    {
        // Создаем тестовый домик
        $house = $this->houseService->createHouse('Тестовый домик', 100.0, 4);
        $houseId = $house->getId();

        // Удаляем домик
        $this->houseService->deleteHouse($house);

        // Проверяем, что домик удален из базы
        $deletedHouse = $this->entityManager->getRepository(House::class)->find($houseId);
        $this->assertNull($deletedHouse);
    }

    protected function tearDown(): void
    {
        // Очищаем базу данных после каждого теста
        if ($this->entityManager) {
            $this->entityManager->getConnection()->executeQuery('TRUNCATE house CASCADE');
            $this->entityManager->close();
            $this->entityManager = null;
        }

        parent::tearDown();
    }
} 