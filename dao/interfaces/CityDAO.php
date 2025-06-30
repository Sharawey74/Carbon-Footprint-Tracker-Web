<?php
namespace Dao\Interfaces;

use Models\City;
use Exceptions\DataAccessException;

interface CityDAO extends DAO {
    public function getBranchIdsByCityId(int $cityId): array;
    public function getCityByName(string $cityName): ?City;
}
?>