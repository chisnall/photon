<?php

namespace App\Core;

use App\Core\Database\Connection;
use App\Core\Traits\GetSetProperty;
use Exception;
use PDOException;
use ReflectionClass;
use Throwable;

final class Application
{
    use GetSetProperty;

    private static Application $app;
    private Controller $controller;
    private Request $request;
    private Response $response;
    private Router $router;
    private Session $session;
    private View $view;
    private Model $user;
    private array $models = [];
    private readonly Connection $db;
    private readonly array $config;
    private int $errors = 0;

    public function __construct()
    {
        // Sets the default exception handler
        set_exception_handler([ExceptionHandler::class, 'framework']);

        // Sets the error handler for PHP errors
        set_error_handler([ErrorHandler::class, 'framework']);

        // Create instances
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->session = new Session('application');
        $this->view = new View();

        // Get application initiator
        $traceInfoFile = Functions::traceInfo("start")['file'];

        // Init config
        $this->initConfig();

        // Get routes
        Functions::includeFile(file: '/app/Routes/routes.php');

        // Connect to database
        $databaseDriver = Functions::getConfig("database/driver");
        $databaseClass = Functions::getConfig("database/$databaseDriver/class");
        $this->db = $databaseClass::getInstance()->getConnection();

        // Return now if running from migrations script
        if ($traceInfoFile == "migrations.php") {
            return;
        }

        // Check if migrations table exists
        try {
            $migrationsCount = $this->db->query("SELECT count(id) FROM migrations")->fetchColumn();
        } catch (Throwable $exception) {
            throw new PDOException(message: "Migrations have not been applied.", previous: $exception);
        }

        // Check migrations have been applied
        if ( $migrationsCount === 0) {
            throw new PDOException(message: "Migrations have not been applied.");
        }

        // Get the user class
        $userClass = Functions::getConfig("class/user");

        // Get primary key property
        $primaryKey = $userClass::primaryKey()['property'];

        // Get user ID
        $primaryValue = $this->session()->get('user/id');

        // Get user data
        if ($primaryValue) {
            $userData = $userClass::getSingleRecord([$primaryKey => $primaryValue]);
        } else {
            $userData = new $userClass();
        }

        // Load user settings
        $userData->loadSettings();

        // Check if user is logged in
        if ($primaryValue) {
            // 1) no user data is available - the user record has been deleted
            // 2) database driver has been changed while the user is logged in
            if (!$userData->getProperty('id') || $this->session()->get('user/dbDriver') != $this->db->driver()) {
                // Logout and exit
                $userClass::logout();

                // Redirect to homepage
                $this->response->redirect('/');
            }
        }

        // Set user
        $this->user = $userData;
    }

    public static function app(): Application
    {
        return self::$app;
    }

    public function controller(): Controller
    {
        return $this->controller;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function view(): View
    {
        return $this->view;
    }

    public function user(): Model
    {
        return $this->user;
    }

    public function model(string $model): ?Model
    {
        return $this->models[$model] ?? null;
    }

    public function db(): Connection
    {
        return $this->db;
    }

    public function setModel(Model $model): void {
        // Get short class name
        $classNameShort = (new ReflectionClass($model))->getShortName();

        // Set model
        $this->models[$classNameShort] = $model;
    }

    public function initConfig()
    {
        // Check config exists
        $configPath = BASE_PATH . '/config.php';
        if (!file_exists($configPath)) {
            // Using code 10 for this - and need to throw standard Exception
            throw new Exception(message: "Config file is missing:\n$configPath", code: 10);
        }

        // Get config array
        $configArray = include $configPath;

        // Handle environment variables
        // These will override the config.php values
        foreach (getenv() as $envKey => $envValue) {
            if (str_starts_with($envKey, 'CONFIG_')) {
                $envKeyArray = explode('_', strtolower($envKey));

                // Ignore the first key: CONFIG
                if (count($envKeyArray) == 3) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]] = $envValue;
                }
                elseif (count($envKeyArray) == 4) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]] = $envValue;
                }
                elseif (count($envKeyArray) == 5) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]][$envKeyArray[4]] = $envValue;
                }
                elseif (count($envKeyArray) == 6) {
                    $configArray[$envKeyArray[1]][$envKeyArray[2]][$envKeyArray[3]][$envKeyArray[4]][$envKeyArray[5]] = $envValue;
                }
            }
        }

        // Set config property
        $this->config = $configArray;
    }

    public function run(): void
    {
        // Do not run this in a try/catch block - we want any exceptions from this
        // to be handled by the exceptionHandler()
        echo $this->router->resolve();
    }
}
