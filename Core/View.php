<?php

namespace Core;

class View
{
    public static function render($view)
    {

        $file = dirname(__DIR__) . '/App/Views/' . $view;
        if (is_readable($file)) {
            require $file;
        } else {
            echo $file . ': Vista no encontrada';
        }
    }

    public static function renderTwig($template, $params = [])
    {

        static $twig = null;

        if (!file_exists(dirname(__DIR__) . '/App/Views/' . $template)) {

            throw new \Exception('Vista: "' . $template . '" no encontrada en la carpeta "App/Views', 500);
        } else {

            if ($twig === null) {
                $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
                $twig = new \Twig\Environment($loader);
            }

            echo $twig->render($template, $params);
        }
    }
}
