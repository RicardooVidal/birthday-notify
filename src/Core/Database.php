<?php

namespace App\Core;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

class Database
{
    /**
     * @var Connection
     */
    private $database;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct($params)
    {
        $this->database = DriverManager::getConnection($params);
        $this->queryBuilder = $this->database->createQueryBuilder();
    }

    public function getBirthdays($day, $month, $user)
    {
        $data = $this->queryBuilder
            ->select('*')
            ->from('birthday_' . $user)
            ->where('day = ? AND month = ?')
            ->setParameter(0, $day)
            ->setParameter(1, $month);

        return $data->fetchAllAssociative();
    }

    public function getData($sql)
    {
        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute();
        return $result->fetchAll();
    }

    public function updateBirthdayData($id, $user)
    {
        $data = $this->queryBuilder
            ->update('birthday_' . $user, 'b')
            ->set('b.years', 'b.years + 1')
            ->where('id = ' . $id);

        $data->executeStatement();
    }
}