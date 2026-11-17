<?php

namespace Controllers;

use Config\Database;
use Models\User;

class HomeController extends BaseController
{
    public function index(): void
    {
        if (!\Middleware\AuthMiddleware::check()) {
            $roleParam = strtolower(trim($_GET['role'] ?? 'customer'));
            $activePortal = in_array($roleParam, ['technician', 'teknisi']) ? 'technician' : 'customer';

            $this->render('auth/login', [
                'pageTitle'           => 'Selamat Datang di Jasaku - SmartService AI',
                'csrfToken'           => \Middleware\AuthMiddleware::generateCsrfToken(),
                'flash'               => \Middleware\AuthMiddleware::getFlash(),
                'activePortal'        => $activePortal,
                'hideAuthButtons'     => true,
                'isWelcomePage'       => true,
                'hideFloatingWidgets' => false,
                'extraCss'            => ['welcome.css'],
                'extraJs'             => ['welcome.js']
            ]);
            return;
        }

        $dbStatus = Database::checkStatus();
        $roles = User::getRoleCards();

        $data = [
            'pageTitle' => 'SmartService AI - Layanan Perbaikan & Perawatan Peralatan Rumah Tangga',
            'dbStatus'  => $dbStatus,
            'roles'     => $roles,
            'appName'   => \Config\Env::get('APP_NAME', 'SmartService AI')
        ];

        $this->render('home', $data);
    }

    public function status(): void
    {
        $dbStatus = Database::checkStatus();
        $this->json([
            'status'    => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'database'  => $dbStatus
        ]);
    }
}
