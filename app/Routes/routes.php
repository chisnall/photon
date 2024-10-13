<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\ProfileController;
use App\Controllers\RegisterController;
use App\Controllers\SettingsController;
use App\Controllers\SiteController;
use App\Controllers\TestsController;

// HomeController
router()->get('/', [HomeController::class, 'home']);
router()->post('/', [HomeController::class, 'home']);
//router()->get('/html', [HomeController::class, 'html']); // disabled and handled natively in /public/html/index.php

// TestsController
router()->get('/tests', [TestsController::class, 'tests']);
router()->post('/tests', [TestsController::class, 'tests']);

// Help
router()->get('/help', 'help/index');
router()->get('/help/configuration', 'help/configuration');
router()->get('/help/misc', 'help/misc');
router()->get('/help/tests', 'help/tests');
router()->get('/help/variables', 'help/variables');

// About
router()->get('/about', 'about');

// Info
router()->get('/migrations', 'migrations');

// LoginController
router()->get('/login', [LoginController::class, 'login']);
router()->post('/login', [LoginController::class, 'login']);
router()->get('/logout', [LoginController::class, 'logout']);

// ProfileController
router()->get('/profile', [ProfileController::class, 'profile']);
router()->post('/profile', [ProfileController::class, 'profile']);

// SettingsController
router()->get('/settings', [SettingsController::class, 'settings']);
router()->get('/settings/defaults', [SettingsController::class, 'defaults']);
router()->post('/settings', [SettingsController::class, 'settings']);

// RegisterController
router()->get('/register', [RegisterController::class, 'register']);
router()->post('/register', [RegisterController::class, 'register']);
router()->get('/register/registered', [RegisterController::class, 'registered']);
router()->get('/register/activate', [RegisterController::class, 'activate']);
router()->get('/register/activated', [RegisterController::class, 'activated']);

// Error page for bad requests
router()->get('/error', [SiteController::class, 'error']);
