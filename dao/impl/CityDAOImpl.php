<?php
namespace Dao\Impl;

use PDO;
use Models\City;
use Dao\Interfaces\CityDAO;
use Exceptions\DataAccessException;

class CityDAOImpl implements CityDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?City {
        $sql = "SELECT * FROM City WHERE CityID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapCity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("City not found: $id", 0, $e);
        }
    }

    public function getCityByName(string $name): ?City {
        $sql = "SELECT * FROM City WHERE CityName = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name]);
            return $stmt->rowCount() ? $this->mapCity($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("City lookup failed: $name", 0, $e);
        }
    }

    public function getBranchIdsByCityId(int $cityId): array {
        $sql = "SELECT BranchID FROM Branch WHERE CityID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cityId]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'BranchID');
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get branch IDs for city: $cityId", 0, $e);
        }
    }

    public function save($object): bool {
        if (!($object instanceof City)) {
            throw new DataAccessException("Object must be an instance of City");
        }
        
        return $object->getCityID() ? $this->update($object) : $this->insert($object);
    }

    public function insert($object): bool {
        if (!($object instanceof City)) {
            throw new DataAccessException("Object must be an instance of City");
        }
        
        try {
            $this->db->beginTransaction();
            
                $sql = "INSERT INTO City (CityName) VALUES (?)";
                $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getCityName()]);
            $object->setCityID($this->db->lastInsertId());
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function update($object): bool {
        if (!($object instanceof City)) {
            throw new DataAccessException("Object must be an instance of City");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE City SET CityName = ? WHERE CityID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getCityName(), $object->getCityID()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete($object): bool {
        if (!($object instanceof City)) {
            throw new DataAccessException("Object must be an instance of City");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM City WHERE CityID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getCityID()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM City";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapCity'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all cities", 0, $e);
        }
    }

    private function mapCity(array $row): City {
        return new City(
            (int)$row['CityID'],
            $row['CityName']
        );
    }
}
?>