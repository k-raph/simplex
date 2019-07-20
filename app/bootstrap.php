<?php

use Simplex\Http\Kernel as HttpKernel;
use Simplex\Kernel;

require "../vendor/autoload.php";

$kernel = new Kernel();
$http = new HttpKernel($kernel);

return $http;
