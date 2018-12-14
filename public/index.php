<?php

use Symfony\Component\HttpFoundation\Request;

$kernel = require '../app/bootstrap.php';

$response = $kernel->handle($request = Request::createFromGlobals());
$kernel->terminate($response, $request);
