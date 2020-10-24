<?php

namespace Core;

class Router
{
    protected $routes;

    public function __construct()
    {
        $this->getRoutes();
    }

    public function getRoutes()
    {
        $content = file_get_contents(dirname(__DIR__) . '/Core/Config/routes.json');
        $this->routes = json_decode($content, true);
    }

    public function getController($uri)
    {
        $uri = ltrim($uri, '/');
        $uri = substr($uri, strpos($uri, '/') + 1);

        if (array_key_exists($uri, $this->routes)) {
            $class = '\\App\Controllers\\' . $this->routes[$uri]['controller'];
            if (class_exists($class)) {
                $reflector = new \ReflectionClass($class);
                return $reflector->newInstance();
            } else {
                throw new \Exception('Controlador (clase): "' . $this->routes[$uri]['controller'] . '" no encontrado en la carpeta "App/Controllers', 500);
            }
        } else {
            throw new \Exception('Ruta no encontrada en la configuración de rutas de la aplicación: ' . $uri, 404);
        }
    }

    public function getAction($uri)
    {
        $uri = ltrim($uri, '/');
        $uri = substr($uri, strpos($uri, '/') + 1);
        return $this->routes[$uri]['action'];
    }
}
