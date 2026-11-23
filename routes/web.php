<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;

// En este archivo definimos todas las rutas de la aplicación.
// Aquí conectamos cada URL que ingresa el usuario con su respectivo controlador.
// Fue un trabajo en equipo donde nos organizamos para dividir las vistas públicas, el login y la zona del dashboard.

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatbotController;

// Estas son las rutas públicas del sistema, accesibles por cualquier visitante sin necesidad de iniciar sesión.

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/espacios', [PublicController::class, 'espacios'])->name('espacios');
Route::get('/cursos', [PublicController::class, 'cursos'])->name('cursos');
Route::get('/auditorio', [PublicController::class, 'auditorio'])->name('auditorio');
Route::get('/profesores', [PublicController::class, 'profesores'])->name('profesores');
Route::post('/chatbot', [ChatbotController::class, 'responder'])->name('chatbot.responder');

// Aquí gestionamos el acceso de los usuarios: desde iniciar sesión hasta crear una cuenta nueva y cerrar sesión.

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/email/verificar/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify.custom');
Route::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Este grupo de rutas maneja la recuperación de contraseñas mediante el envío de un enlace seguro por correo electrónico.
Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');

// Agrupamos bajo este middleware las rutas que requieren que el usuario esté obligatoriamente autenticado en el sistema.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Aquí el estudiante visualiza el mapa interactivo de asientos, reserva un lugar y puede cancelar su ticket.
    Route::get('/reserva/{funcion}', [ReservaController::class, 'showMap'])->name('reserva.mapa');
    Route::post('/reserva/{funcion}', [ReservaController::class, 'store'])->name('reserva.store');
    Route::delete('/reserva/ticket/{ticket_id}', [ReservaController::class, 'cancelar'])->name('reserva.cancelar');

    // Estas rutas permiten al usuario ver y actualizar la información de su perfil personal.
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');

    // Rutas exclusivas del Administrador para gestionar la página en tiempo real y aprobar pagos del Auditorio.
    Route::post('/admin/tickets/{ticket_id}/aprobar', [AdminController::class, 'aprobarTicket'])->name('admin.tickets.aprobar');
    Route::match(['post', 'delete'], '/admin/tickets/{ticket_id}/rechazar', [AdminController::class, 'rechazarTicket'])->name('admin.tickets.rechazar');
    Route::post('/admin/auditorio/asignar-manual', [AdminController::class, 'asignarManual'])->name('admin.auditorio.asignar');
    Route::match(['post', 'put'], '/admin/salones/{salon_id}', [AdminController::class, 'updateSalon'])->name('admin.salones.update');
    Route::match(['post', 'put'], '/admin/cursos/{curso_id}', [AdminController::class, 'updateCurso'])->name('admin.cursos.update');
    Route::post('/admin/cursos', [AdminController::class, 'storeCurso'])->name('admin.cursos.store');
    Route::match(['post', 'put'], '/admin/profesores/{profesor_id}', [AdminController::class, 'updateProfesor'])->name('admin.profesores.update');
    Route::post('/admin/profesores', [AdminController::class, 'storeProfesor'])->name('admin.profesores.store');
});
