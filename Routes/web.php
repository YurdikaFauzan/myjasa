<?php

use Routes\Router;
use Controllers\HomeController;
use Controllers\AuthController;
use Controllers\AdminController;
use Controllers\CustomerController;
use Controllers\TechnicianController;
use Controllers\CategoryController;
use Controllers\ServiceController;
use Controllers\AiConsultationController;
use Controllers\OtpAuthController;
use Controllers\ProfileController;

// Rute Halaman Utama & Status System
Router::get('/', [HomeController::class, 'index']);
Router::get('/status', [HomeController::class, 'status']);

// Rute Profil Pengguna (Customer, Technician, Admin)
Router::get('/profile', [ProfileController::class, 'show']);
Router::post('/profile/update', [ProfileController::class, 'update']);
Router::post('/profile/change-password', [ProfileController::class, 'changePassword']);
Router::get('/customer/profile', [ProfileController::class, 'show']);
Router::get('/technician/profile', [ProfileController::class, 'show']);
Router::get('/admin/profile', [ProfileController::class, 'show']);

// Rute Autentikasi (Guest Access & Dual Login Portals)
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);

Router::get('/login/customer', [AuthController::class, 'showCustomerLogin']);
Router::post('/login/customer', [AuthController::class, 'login']);

Router::get('/login/technician', [AuthController::class, 'showTechnicianLogin']);
Router::post('/login/technician', [AuthController::class, 'login']);

Router::get('/login/admin', [AuthController::class, 'showAdminLogin']);
Router::post('/login/admin', [AuthController::class, 'processAdminLogin']);
Router::get('/admin/login', [AuthController::class, 'showAdminLogin']);
Router::post('/admin/login', [AuthController::class, 'processAdminLogin']);

Router::get('/register', [AuthController::class, 'showRegister']);
Router::post('/register', [AuthController::class, 'register']);

Router::get('/register/customer', [AuthController::class, 'showCustomerRegister']);
Router::post('/register/customer', [AuthController::class, 'register']);

Router::get('/register/technician', [AuthController::class, 'showTechnicianRegister']);
Router::post('/register/technician', [AuthController::class, 'register']);

Router::get('/register/admin', [AuthController::class, 'showAdminRegister']);
Router::post('/register/admin', [AuthController::class, 'registerAdmin']);
Router::get('/admin/register', [AuthController::class, 'showAdminRegister']);
Router::post('/admin/register', [AuthController::class, 'registerAdmin']);

Router::get('/logout', [AuthController::class, 'logout']);
Router::post('/logout', [AuthController::class, 'logout']);

Router::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Router::post('/forgot-password', [AuthController::class, 'processForgotPassword']);

// Rute Autentikasi WhatsApp OTP (Fonte Service)
Router::get('/otp/login', [OtpAuthController::class, 'showPhoneForm']);
Router::post('/otp/send', [OtpAuthController::class, 'sendOtp']);
Router::get('/otp/verify', [OtpAuthController::class, 'showVerifyForm']);
Router::post('/otp/verify', [OtpAuthController::class, 'verifyOtp']);
Router::post('/otp/resend', [OtpAuthController::class, 'resendOtp']);
Router::get('/otp/cancel', [OtpAuthController::class, 'cancelOtp']);
Router::post('/otp/cancel', [OtpAuthController::class, 'cancelOtp']);
Router::get('/api/fonte/status', [OtpAuthController::class, 'apiGatewayStatus']);

// Rute Dashboard Berdasarkan Role (Role-Based Access Control)
Router::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Router::get('/customer/dashboard', [CustomerController::class, 'dashboard']);
Router::get('/technician/dashboard', [TechnicianController::class, 'dashboard']);

// Rute Pengaturan Sistem Model AI Multi-Provider
Router::get('/admin/ai-config', [AdminController::class, 'aiConfig']);
Router::get('/ai-config', [AdminController::class, 'aiConfig']);
Router::get('/customer/ai-config', [AdminController::class, 'aiConfig']);
Router::post('/admin/ai-config/save', [AdminController::class, 'saveAiConfig']);
Router::post('/admin/ai-config/test', [AdminController::class, 'testAiModel']);
Router::post('/admin/ai-config/fetch-models', [AdminController::class, 'fetchModels']);
Router::post('/admin/ai-config/delete-key', [AdminController::class, 'deleteApiKey']);
Router::post('/admin/ai-config/reset', [AdminController::class, 'resetAiConfig']);

// Rute Fitur & Workflow Modul Teknisi (Technician Only)
Router::get('/technician/jobs', [TechnicianController::class, 'listJobs']);
Router::get('/technician/jobs/detail', [TechnicianController::class, 'showJobDetail']);
Router::post('/technician/jobs/accept', [TechnicianController::class, 'acceptJob']);
Router::post('/technician/jobs/reject', [TechnicianController::class, 'rejectJob']);
Router::post('/technician/jobs/update-status', [TechnicianController::class, 'updateStatus']);
Router::get('/technician/jobs/complete', [TechnicianController::class, 'showCompletionForm']);
Router::post('/technician/jobs/complete', [TechnicianController::class, 'completeJob']);

// Rute Manajemen Permintaan Jasa & Penugasan Teknisi (Admin Only)
Router::get('/admin/requests', [AdminController::class, 'listRequests']);
Router::get('/admin/requests/detail', [AdminController::class, 'showRequestDetail']);
Router::post('/admin/requests/assign', [AdminController::class, 'assignTechnician']);

// Rute Fitur Booking & History Customer
Router::get('/customer/requests', [CustomerController::class, 'listBookings']);
Router::get('/customer/requests/create', [CustomerController::class, 'showBookingForm']);
Router::post('/customer/requests/create', [CustomerController::class, 'createBooking']);
Router::get('/customer/requests/detail', [CustomerController::class, 'showBookingDetail']);
Router::get('/customer/requests/pay', [CustomerController::class, 'showPaymentForm']);
Router::post('/customer/requests/pay', [CustomerController::class, 'processPayment']);
Router::post('/customer/requests/cancel', [CustomerController::class, 'cancelBooking']);
Router::post('/customer/requests/review', [CustomerController::class, 'submitReview']);
Router::get('/customer/history', [CustomerController::class, 'history']);
Router::get('/customer/api/services-by-category', [CustomerController::class, 'getServicesByCategory']);

// Rute Riwayat & Tracking (Admin & Technician)
Router::get('/technician/history', [TechnicianController::class, 'history']);
Router::get('/admin/history', [AdminController::class, 'history']);

// Rute Fitur Konsultasi AI Diagnosis (Customer Only)
Router::get('/customer/ai-consultation', [AiConsultationController::class, 'index']);
Router::post('/customer/ai-consultation', [AiConsultationController::class, 'diagnose']);
Router::get('/customer/ai-consultation/history', [AiConsultationController::class, 'history']);

// Rute Master Data Category Management (Admin Only)
Router::get('/admin/categories', [CategoryController::class, 'index']);
Router::post('/admin/categories/create', [CategoryController::class, 'create']);
Router::post('/admin/categories/edit', [CategoryController::class, 'edit']);
Router::post('/admin/categories/delete', [CategoryController::class, 'delete']);
Router::post('/admin/categories/toggle-status', [CategoryController::class, 'toggleStatus']);

// Rute Master Data Service Management (Admin Only)
Router::get('/admin/services', [ServiceController::class, 'index']);
Router::post('/admin/services/create', [ServiceController::class, 'create']);
Router::post('/admin/services/edit', [ServiceController::class, 'edit']);
Router::post('/admin/services/delete', [ServiceController::class, 'delete']);
Router::post('/admin/services/toggle-status', [ServiceController::class, 'toggleStatus']);

// Rute Manajemen Pengguna & Pendaftaran Akun (Admin Only)
Router::get('/admin/users', [AdminController::class, 'listUsers']);
Router::post('/admin/users/create', [AdminController::class, 'createUser']);
Router::post('/admin/users/edit', [AdminController::class, 'editUser']);
Router::post('/admin/users/delete', [AdminController::class, 'deleteUser']);

// Rute Chatbot CS & Quick Model Switch (Global/Public API)
Router::post('/api/chatbot', [\Controllers\ChatbotController::class, 'chat']);
Router::post('/api/chatbot/reset', [\Controllers\ChatbotController::class, 'reset']);
Router::post('/api/chatbot/switch-model', [\Controllers\ChatbotController::class, 'switchModel']);


