<?php

namespace Stan\Services;

use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AutowireContainer {
    protected static $containerBuilder;
    protected static $autowired = [];

    public static function boot() {
        static::$containerBuilder = new ContainerBuilder();
        static::$autowired = [];
    }

    public function autowireClasses($classes) {
        foreach ($classes as $class) {
            if (!array_key_exists($class, static::$autowired)) {
                static::$autowired[$class] = false;
            }
        }

        while (in_array(false, array_values(static::$autowired))) {
            foreach (static::$autowired as $class => $isWired) {
                if ($isWired) {
                    continue;
                }
                $definition = static::$containerBuilder->autowire($class, $class);
                $rc = new ReflectionClass($class);
                if ($rc->hasMethod('__construct')) {
                    $constructor = $rc->getMethod('__construct');
                    foreach ($constructor->getParameters() as $parameter) {
                        if ($parameter->isDefaultValueAvailable()) {
                            $defaultValue = $parameter->getDefaultValue();
                            $definition->addArgument($defaultValue);
                        } elseif ($parameter->getClass()) {
                            $parameterClassName = $parameter->getClass()->getName();
                            $definition->addArgument(new Reference($parameterClassName));
                            if (!static::$containerBuilder->has($parameterClassName) && !array_key_exists($parameterClassName, static::$autowired)) {
                                static::$autowired[$parameterClassName] = false;
                            }
                        } else {
                            throw new Exception("Cannot autowire");
                        }
                    }
                }
                static::$autowired[$class] = true;
            }
        }
    }

    public function get($class) {
        return static::$containerBuilder->get($class);
    }
}


