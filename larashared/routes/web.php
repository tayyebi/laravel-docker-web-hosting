<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WizardController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::middleware('auth')->group(function () {

    Route::get('/', [WizardController::class, 'showDomainSelection'])->name('wizard.domain');
    Route::post('/wizard/domain', [WizardController::class, 'processDomainSelection'])->name('wizard.processDomain');
    Route::delete('/wizard/domain/{domain}', [WizardController::class, 'deleteDomain'])->name('wizard.deleteDomain');

    Route::get('/wizard/plan/{domain_id}', [WizardController::class, 'showPlanSelection'])->name('wizard.plan');
    Route::post('/wizard/plan/{domain_id}', [WizardController::class, 'processPlanSelection'])->name('wizard.processPlan');

    Route::get('/wizard/summary/{website_id}', [WizardController::class, 'showSummary'])->name('wizard.summary');
    Route::delete('/wizard/website/{website}', [WizardController::class, 'deleteWebsite'])->name('wizard.deleteWebsite');
});
