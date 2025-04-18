<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Найти все бронирования для конкретного домика
     */
    public function findByHouse($house): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.house = :house')
            ->setParameter('house', $house)
            ->orderBy('b.checkInDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Проверить доступность домика на определенные даты
     */
    public function isHouseAvailable($house, \DateTimeInterface $checkIn, \DateTimeInterface $checkOut): bool
    {
        $overlappingBookings = $this->createQueryBuilder('b')
            ->andWhere('b.house = :house')
            ->andWhere('b.status != :cancelledStatus')
            ->andWhere(
                '(b.checkInDate <= :checkOut AND b.checkOutDate >= :checkIn)'
            )
            ->setParameter('house', $house)
            ->setParameter('cancelledStatus', 'cancelled')
            ->setParameter('checkIn', $checkIn)
            ->setParameter('checkOut', $checkOut)
            ->getQuery()
            ->getResult();

        return count($overlappingBookings) === 0;
    }
}