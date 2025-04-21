<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\House;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createBooking(
        House $house,
        string $clientName,
        string $clientEmail,
        \DateTimeInterface $checkInDate,
        \DateTimeInterface $checkOutDate,
        int $numberOfGuests,
        ?string $clientPhone = null
    ): Booking {
        $booking = new Booking();
        $booking->setHouse($house);
        $booking->setClientName($clientName);
        $booking->setClientEmail($clientEmail);
        $booking->setClientPhone($clientPhone);
        $booking->setCheckInDate($checkInDate);
        $booking->setCheckOutDate($checkOutDate);
        $booking->setNumberOfGuests($numberOfGuests);
        
        // Рассчитываем общую стоимость
        $nights = $checkInDate->diff($checkOutDate)->days;
        $totalPrice = $house->getPricePerNight() * $nights;
        $booking->setTotalPrice($totalPrice);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    public function getBookingsByHouse(House $house): array
    {
        return $this->entityManager->getRepository(Booking::class)
            ->findBy(['house' => $house], ['checkInDate' => 'ASC']);
    }

    public function isHouseAvailable(House $house, \DateTimeInterface $checkIn, \DateTimeInterface $checkOut): bool
    {
        return $this->entityManager->getRepository(Booking::class)
            ->isHouseAvailable($house, $checkIn, $checkOut);
    }

    public function cancelBooking(Booking $booking): void
    {
        $booking->setStatus('cancelled');
        $this->entityManager->flush();
    }
}