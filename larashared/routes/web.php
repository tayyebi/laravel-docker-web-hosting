<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomainsController;
use App\Http\Controllers\WebsitesController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::middleware('auth')->group(function () {
    Route::get('/packages', [DashboardController::class, 'packages'])->name('packages');

    Route::resource('domains', DomainsController::class);

    Route::get('/emails', [DashboardController::class, 'emails'])->name('emails');

    Route::get('/websites', [WebsitesController::class, 'index'])->name('websites.index');
    Route::get('/websites/{website}/show', [WebsitesController::class, 'show'])->name('websites.show');
    Route::get('/websites/create', [WebsitesController::class, 'create'])->name('websites.create');
    Route::post('/websites/store', [WebsitesController::class, 'store'])->name('websites.store');
    Route::get('/websites/{website}/edit', [WebsitesController::class, 'edit'])->name('websites.edit');
    Route::put('/websites/{website}/update', [WebsitesController::class, 'update'])->name('websites.update');
    Route::delete('/websites/{website}/destroy', [WebsitesController::class, 'destroy'])->name('websites.destroy');

    Route::get('/databases', [DashboardController::class, 'databases'])->name('databases');

    Route::get('/images', [ImagesController::class, 'index'])->name('images');
    Route::post('/images/build', [ImagesController::class, 'build'])->name('images.build');

    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::post('/file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
    Route::delete('/file-manager/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
    Route::get('/file-manager/edit', [FileManagerController::class, 'edit'])->name('file-manager.edit');
    Route::post('/file-manager/save', [FileManagerController::class, 'save'])->name('file-manager.save');
    Route::post('/file-manager/compress', [FileManagerController::class, 'compress'])->name('file-manager.compress');
    Route::post('/file-manager/extract', [FileManagerController::class, 'extract'])->name('file-manager.extract');
    Route::get('/file-manager/refresh', [FileManagerController::class, 'refresh'])->name('file-manager.refresh');
});
