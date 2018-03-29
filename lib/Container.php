<?php

namespace Lib;

class Container
{
    protected $binds;

    public function bind($abstract, $concrete)
    {
 		if (is_object($concrete)) {
 			$closure = $concrete;
    	} else {
	        $closure = function ($app) use ($abstract, $concrete) {
	            $method = $abstract == $concrete ? 'build' : 'make';
	            return $app->$method($concrete);
	        };
    	}
    	$this->binds[$abstract] = $closure;
    }

    public function make($abstract)
    {
        $concrete = $this->getConrete($abstract);
        if ($this->isBuildable($abstract, $concrete)) {
            return $this->build($abstract, $concrete);
        }
        return $this->make($concrete);
    }

    public function isBuildable($abstract, $concrete)
    {
    	return $abstract === $concrete || $concrete instanceof \Closure || is_object($concrete);
    }

    public function getConrete($abstract)
    {
        if (!isset($this->binds[$abstract])) {
            return $abstract;
        }
        return $this->binds[$abstract];
    }

    public function build($abstract, $concrete)
    {
    	if($concrete instanceof \Closure){
    		return $concrete($this);
    	} elseif(is_object($concrete)){
    		return $concrete;
    	}

        $reflector = new \ReflectionClass($concrete);
        $constructor     = $reflector->getConstructor();
        if (is_null($constructor)) {
        	var_dump($concrete);
            return new $concrete;
        }
        $parameters   = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);
        return $reflector->newInstanceArgs($dependencies);
    }

    public function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = null;
            } else {
                $dependencies[] = $this->make($dependency->name);
            }
        }
        return $dependencies;
    }
}
