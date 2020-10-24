<?php

namespace Core;

abstract class Controller extends Security
{
    public function callAction($action, $params = [])
    {
        if (method_exists($this, $action)) {
            $array_params = [$params];
            return call_user_func_array([$this, $action], $array_params);
        } else {
            throw new \Exception('Acción (método): "' . $action . '" no encontrado en el controlador', 500);
        }
    }
}
