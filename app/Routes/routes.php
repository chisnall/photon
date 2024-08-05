<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ProfileController;
use App\Controllers\RegisterController;
use App\Controllers\SettingsController;
use App\Controllers\SiteController;
use App\Controllers\TestsController;
use App\Core\Application;


// HomeController
Application::app()->router()->get('/', [HomeController::class, 'home']);
Application::app()->router()->post('/', [HomeController::class, 'home']);
Application::app()->router()->get('/html', [HomeController::class, 'html']);

// TestsController
Application::app()->router()->get('/tests', [TestsController::class, 'tests']);
Application::app()->router()->post('/tests', [TestsController::class, 'tests']);

// Help
Application::app()->router()->get('/help', 'help/index');
Application::app()->router()->get('/help/configuration', 'help/configuration');
Application::app()->router()->get('/help/misc', 'help/misc');
Application::app()->router()->get('/help/tests', 'help/tests');
Application::app()->router()->get('/help/variables', 'help/variables');

// About
Application::app()->router()->get('/about', 'about');

// Info
Application::app()->router()->get('/migrations', 'migrations');

// LoginController
Application::app()->router()->get('/login', [LoginController::class, 'login']);
Application::app()->router()->post('/login', [LoginController::class, 'login']);
Application::app()->router()->get('/logout', [LoginController::class, 'logout']);

// ProfileController
Application::app()->router()->get('/profile', [ProfileController::class, 'profile']);
Application::app()->router()->post('/profile', [ProfileController::class, 'profile']);

// SettingsController
Application::app()->router()->get('/settings', [SettingsController::class, 'settings']);
Application::app()->router()->get('/settings/defaults', [SettingsController::class, 'defaults']);
Application::app()->router()->post('/settings', [SettingsController::class, 'settings']);

// RegisterController
Application::app()->router()->get('/register', [RegisterController::class, 'register']);
Application::app()->router()->post('/register', [RegisterController::class, 'register']);
Application::app()->router()->get('/register/registered', [RegisterController::class, 'registered']);
Application::app()->router()->get('/register/activate', [RegisterController::class, 'activate']);
Application::app()->router()->get('/register/activated', [RegisterController::class, 'activated']);

// Error page for bad requests
Application::app()->router()->get('/error', [SiteController::class, 'error']);
