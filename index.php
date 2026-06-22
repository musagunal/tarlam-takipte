<?php

session_start();

require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/SimplePdf.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Models/Field.php';
require_once __DIR__ . '/app/Controllers/PageController.php';

$router = new Router();
$controller = new PageController();

$router->get('', [$controller, 'login']);
$router->get('login', [$controller, 'login']);
$router->get('sifremi-unuttum', [$controller, 'forgotPassword']);
$router->get('register', [$controller, 'register']);
$router->get('anasayfa', [$controller, 'home']);
$router->get('kayit', [$controller, 'quickCreate']);
$router->get('normal-kayit', [$controller, 'normalCreate']);
$router->get('detayli-kayit', [$controller, 'detailedCreate']);
$router->get('tarlalar', [$controller, 'fields']);
$router->get('tarlalar/pdf', [$controller, 'fieldsPdf']);
$router->get('hesap', [$controller, 'account']);

$router->post('login', [$controller, 'loginPost']);
$router->post('sifremi-unuttum', [$controller, 'forgotPasswordPost']);
$router->post('register', [$controller, 'registerPost']);
$router->post('logout', [$controller, 'logout']);
$router->post('account/password', [$controller, 'changePassword']);
$router->post('fields/store', [$controller, 'storeField']);
$router->post('fields/update', [$controller, 'updateField']);
$router->post('fields/delete', [$controller, 'deleteField']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
