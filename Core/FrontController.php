<?php

namespace Core;

class FrontController
{
    protected $routing;
    public $request_uri;
    public $params;

    public function __construct()
    {
        $this->routing = new Router;
    }

    public function run()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'];
        $this->request_uri = parse_url($this->request_uri);
        $this->request_uri = $this->request_uri['path'];
        $this->params = array_merge($_GET, $_POST);
        $controller = $this->routing->getController($this->request_uri);
        $action = $this->routing->getAction($this->request_uri);
        $controller->callAction($action, $this->params);
        exit;
    }
}
