<?php
namespace Utils;

/**
 * Simple dependency container to instantiate and manage services
 */
class DependencyContainer {
    private $services = [];
    private $factories = [];
    
    /**
     * Register a service with a factory function
     * 
     * @param string $id Service identifier (usually class name)
     * @param callable $factory Factory function to create the service
     */
    public function register($id, callable $factory) {
        $this->factories[$id] = $factory;
    }
    
    /**
     * Get a service instance
     * 
     * @param string $id Service identifier
     * @return mixed Service instance
     * @throws \Exception If service not found
     */
    public function get($id) {
        // If already instantiated, return cached instance
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }
        
        // If factory exists, create instance
        if (isset($this->factories[$id])) {
            $this->services[$id] = $this->factories[$id]($this);
            return $this->services[$id];
        }
        
        // Service not found
        throw new \Exception("Service $id not found in container");
    }
    
    /**
     * Check if a service is registered
     * 
     * @param string $id Service identifier
     * @return bool True if service exists
     */
    public function has($id) {
        return isset($this->services[$id]) || isset($this->factories[$id]);
    }
} 