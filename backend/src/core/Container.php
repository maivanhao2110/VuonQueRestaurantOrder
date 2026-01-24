<?php
/**
 * Simple Dependency Injection Container
 */
class Container {
    private $services = [];

    public function set($name, $closure) {
        $this->services[$name] = $closure;
    }

    public function get($name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service not found: " . $name);
        }

        $service = $this->services[$name];
        
        // If it's a closure, resolve it once (Singleton within container scope)
        // Or we can just return a new instance every time. 
        // For services/repos, singleton is usually fine or preferred.
        if (is_callable($service)) {
            $this->services[$name] = $service($this);
            return $this->services[$name];
        }

        return $service;
    }
}
?>
