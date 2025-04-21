<?php

namespace App\Tests\Service;

use App\Entity\Booking;
use App\Entity\House;
use App\Service\BookingService;
use App\Service\HouseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookingServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?BookingService $bookingService = null;
    private ?HouseService $houseService = null;
    private ?House $testHouse = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel(['environment' => 'test']);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        
        $this->entityManager->getConnection()->executeQuery('TRUNCATE house, booking CASCADE');
        
        $this->bookingService = new BookingService($this->entityManager);
        $this->houseService = new HouseService($this->entityManager);
        
        $this->testHouse = $this->houseService->createHouse(
            'Тестовый домик',
            100.0,
            4,
            'Описание тестового домика'
        );
        
        $this->entityManager->flush();
    }

    public function testCreateBooking(): void
    {
        $booking = $this->bookingService->createBooking(
            $this->testHouse,
            'Иван Иванов',
            'ivan@example.com',
            new \DateTime('2024-06-01'),
            new \DateTime('2024-06-05'),
            2,
            '+7 999 123 45 67'
        );

        $this->assertNotNull($booking->getId());
        $this->assertEquals($this->testHouse->getId(), $booking->getHouse()->getId());
        $this->assertEquals('Иван Иванов', $booking->getClientName());
        $this->assertEquals('ivan@example.com', $booking->getClientEmail());
        $this->assertEquals('+7 999 123 45 67', $booking->getClientPhone());
        $this->assertEquals(2, $booking->getNumberOfGuests());
        $this->assertEquals(400.0, $booking->getTotalPrice());

        $savedBooking = $this->entityManager->getRepository(Booking::class)->find($booking->getId());
        $this->assertNotNull($savedBooking);
        $this->assertEquals($booking->getClientName(), $savedBooking->getClientName());
    }

    public function testGetBookingsByHouse(): void
    {
        $booking1 = $this->bookingService->createBooking(
            $this->testHouse,
            'Клиент 1',
            'client1@example.com',
            new \DateTime('2024-07-01'),
            new \DateTime('2024-07-05'),
            2
        );

        $booking2 = $this->bookingService->createBooking(
            $this->testHouse,
            'Клиент 2',
            'client2@example.com',
            new \DateTime('2024-07-10'),
            new \DateTime('2024-07-15'),
            3
        );

        $this->entityManager->flush();

        $bookings = $this->bookingService->getBookingsByHouse($this->testHouse);

        $this->assertCount(2, $bookings);
        $this->assertContainsEquals($booking1, $bookings);
        $this->assertContainsEquals($booking2, $bookings);
    }

    public function testIsHouseAvailable(): void
    {
        $this->bookingService->createBooking(
            $this->testHouse,
            'Тест',
            'test@example.com',
            new \DateTime('2024-08-01'),
            new \DateTime('2024-08-05'),
            2
        );

        $this->entityManager->flush();

        $this->assertTrue($this->bookingService->isHouseAvailable(
            $this->testHouse,
            new \DateTime('2024-07-01'),
            new \DateTime('2024-07-05')
        ));

        $this->assertFalse($this->bookingService->isHouseAvailable(
            $this->testHouse,
            new \DateTime('2024-08-03'),
            new \DateTime('2024-08-07')
        ));

        $this->assertTrue($this->bookingService->isHouseAvailable(
            $this->testHouse,
            new \DateTime('2024-08-06'),
            new \DateTime('2024-08-10')
        ));
    }

    public function testCancelBooking(): void
    {
        $booking = $this->bookingService->createBooking(
            $this->testHouse,
            'Тест',
            'test@example.com',
            new \DateTime('2024-09-01'),
            new \DateTime('2024-09-05'),
            2
        );

        $this->entityManager->flush();

        $this->bookingService->cancelBooking($booking);

        $this->assertEquals('cancelled', $booking->getStatus());

        $this->entityManager->clear();
        $savedBooking = $this->entityManager->getRepository(Booking::class)->find($booking->getId());
        $this->assertEquals('cancelled', $savedBooking->getStatus());
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->getConnection()->executeQuery('TRUNCATE house, booking CASCADE');
            $this->entityManager->close();
            $this->entityManager = null;
        }
        parent::tearDown();
    }
}