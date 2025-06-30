<?php
/**
 * City model representing a city in the database
 */
namespace Models;

class City {
    private $cityID;
    private $cityName;
    
    /**
     * Constructor
     * 
     * @param int $cityID The city ID
     * @param string $cityName The city name
     */
    public function __construct($cityID = null, $cityName = null) {
        $this->cityID = $cityID;
        $this->cityName = $cityName;
    }
    
    // Getters
    public function getCityID() {
        return $this->cityID;
    }
    
    public function getCityName() {
        return $this->cityName;
    }
    
    // Setters
    public function setCityID($cityID) {
        $this->cityID = $cityID;
    }
    
    public function setCityName($cityName) {
        $this->cityName = $cityName;
    }
}
?>
