<?php

declare(strict_types=1);

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $scriptDir === '/' ? '' : $scriptDir);

require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/models/Employee.php';
require_once __DIR__ . '/../app/controllers/EmployeeController.php';

$router = new Router();

//Redirect halaman awal "/" ke "/employees"
$router->get('/', [EmployeeController::class, 'index']);

//Route wajib 
$router->get('/employees', [EmployeeController::class, 'index']);
$router->get('/employees/create', [EmployeeController::class, 'create']);
$router->post('/employees/store', [EmployeeController::class, 'store']);
$router->get('/employees/edit', [EmployeeController::class, 'edit']);
$router->post('/employees/update', [EmployeeController::class, 'update']);
$router->post('/employees/delete', [EmployeeController::class, 'destroy']);

$router->dispatch();
