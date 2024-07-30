<?php

namespace App\Functions;

use App\Core\Application;
use App\Core\Functions;
use App\Exception\AppException;

class Output
{
    public static function dbInfo(): string
    {
        // Driver types
        $dbDriverType = [
            "maria" => "MariaDB",
            "mysql" => "MySQL",
            "pgsql" => "PostgreSQL",
            "sqlite" => "SQLite",
        ];

        // Icon views
        $dbDriverIcon = [
            "maria" => "database-mariadb",
            "mysql" => "database-mysql",
            "pgsql" => "database-postgresql",
            "sqlite" => "database-sqlite",
        ];

        // Get database name and version
        $dbDriverName = Application::app()->db()->driver();
        $dbServerVersion = Application::app()->db()->version();

        if ($dbDriverName == "mysql") {
            // Check for MariaDB
            if (str_contains(strtolower($dbServerVersion), 'mariadb')) {
                $dbDriverName = 'maria';
            }

            // Trim version string
            // 11.3.2-MariaDB-1:11.3.2+maria~ubu2204
            $dbServerVersion = trim(preg_replace("/-.*/", '', $dbServerVersion));
        } elseif ($dbDriverName == "pgsql") {
            // Trim version string
            $dbServerVersion = trim(preg_replace("/\(.*/", '', $dbServerVersion));
        }

        // Set info
        $dbInfo = "<div>" . $dbDriverType[$dbDriverName] . " $dbServerVersion</div>";

        // Check for icons enabled
        if (Functions::getConfig('page/footer/databaseIcons/show')) {
            // Get icon
            $output = self::icon($dbDriverIcon[$dbDriverName]);

            // Append icon to info
            $dbInfo .= $output;
        }

        // Return
        return $dbInfo;
    }

    public static function icon($icon): string
    {
        // Set icon path
        $iconPath = BASE_PATH . "/app/Views/icons/$icon.php";

        // Check icon path exists
        if (!file_exists($iconPath)) {
            throw new AppException(message: "Icon file missing: $iconPath");
        }

        // Get icon view
        ob_start();
        include $iconPath;
        $output = ob_get_clean();

        // Return
        return $output;
    }

    public static function ageFormat(int $timestamp): string
    {
        // Calculate age
        $ageSeconds = time() - $timestamp;
        $ageMinutes = (int)round($ageSeconds / 60, 0);
        $ageHours = (int)round($ageSeconds / 3600, 0);

        // Return formatted age
        if ( $ageSeconds < 10)      return "just now";
        elseif ( $ageSeconds < 60)  return "$ageSeconds seconds ago";
        elseif ( $ageMinutes == 1)  return "1 minute ago";
        elseif ( $ageMinutes < 60)  return "$ageMinutes minutes ago";
        elseif ( $ageHours == 1)    return "1 hour ago";
        else                        return "$ageHours hours ago";
    }
}
