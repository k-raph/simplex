<?php

use Simplex\Kernel;
use Symfony\Component\HttpFoundation\Request;

require "../vendor/autoload.php";

$kernel = new Kernel();

$response = $kernel->handle($request = Request::createFromGlobals());
$kernel->terminate($response, $request);