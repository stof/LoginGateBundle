<?php

namespace Anyx\LoginGateBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * 
 */
class FailureLoginAttemptRepository extends EntityRepository
{
    /**
     * 
     * @param string $ip
     * @param \DateTime $startDate
     * @return integer
     */
    public function getCountAttempts($ip, \DateTime $startDate)
    {
        if (!is_int($ip)) {
            $ip = ip2long($ip);
        }
        
        return $this->createQueryBuilder('attempt')
                    ->select('COUNT(attempt.id)')
                    ->where('attempt.ip = :ip')
                    ->andWhere('attempt.createdAt > :createdAt')
                    ->setParameters(array(
                        'ip'        => $ip,
                        'createdAt' => $startDate
                    ))
                    ->getQuery()
                    ->getSingleScalarResult()
        ;
    }
    
    /**
     * 
     * @param integer $ip
     * @return \Anyx\LoginGateBundle\Entity\FailureLoginAttempt | null
     */
    public function getLastAttempt($ip)
    {
        if (!is_int($ip)) {
            $ip = ip2long($ip);
        }
        return $this->createQueryBuilder('attempt')
                    ->where('attempt.ip = :ip')
                    ->orderBy('attempt.createdAt', 'DESC')
                    ->setParameters(array(
                        'ip'        => $ip
                    ))
                    ->getQuery()
                    ->setMaxResults(1)
                    ->getOneOrNullResult()
        ;
    }
    
    /**
     * 
     * @param integer $ip
     * @return integer
     */
    public function clearAttempts($ip)
    {
        if (!is_int($ip)) {
            $ip = ip2long($ip);
        }
        
        return $this->getEntityManager()
                ->createQuery('DELETE FROM ' . $this->getClassMetadata()->name . ' attempt WHERE attempt.ip = ' . intval($ip))
                ->execute()
            ;
    }
}
