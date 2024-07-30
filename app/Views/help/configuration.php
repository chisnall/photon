<?php
$title = 'Help: Configuration';
?>
<h2 class="mb-4 text-2xl font-bold">Configuration</h2>

<h2 class="mt-6 mb-2 text-xl font-bold">Intro</h2>
<p class="mb-2">Changes to the application configuration is done by one of two ways:</p>
<ol class="mb-2 list-decimal list-inside">
    <li>Environment variables (the recommended way for containers)</li>
    <li>Editing the <span class="font-mono font-bold">config.php</span> file in the root directory (for other systems)</li>
</ol>

<h2 class="mt-6 mb-2 text-xl font-bold">Format</h2>
<p class="mb-2">The configuration hierarchy is described in the following table.</p>
<p class="mb-2">The only values you should change are:</p>
<ol class="mb-2 list-decimal list-inside">
    <li>Database configuration, if you are switching from the default SQLite to a dedicated database server, or changing the path to the SQLite database</li>
    <li>The system username of the Nginx or Apache service if the system username is not the default <em>www-data</em></li>
</ol>
<table class="mt-4 text-left border-collapse">
    <tr>
        <th class="table-heading pr-6">database</th>
        <td class="table-cell pr-6"></td>
        <td class="table-cell pr-6"></td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6"></td>
        <th class="table-heading pr-6">driver</th>
        <td class="table-cell pr-6"><em>mysql | pgsql | sqlite</em></td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6"></td>
        <th class="table-heading pr-6">mysql</th>
        <td class="table-cell pr-6">MySQL configuration</td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6"></td>
        <th class="table-heading pr-6">pgsql</th>
        <td class="table-cell pr-6">PostgreSQL configuration</td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6"></td>
        <th class="table-heading pr-6">sqlite</th>
        <td class="table-cell pr-6">SQLite configuration</td>
    </tr>
    <tr>
        <th class="table-heading pr-6">app</th>
        <td class="table-cell pr-6"></td>
        <td class="table-cell pr-6"></td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6"></td>
        <th class="table-heading pr-6">httpUser</th>
        <td class="table-cell pr-6">Username of the Nginx or Apache service</td>
    </tr>
</table>

<h2 class="mt-6 mb-2 text-xl font-bold">Environment variables</h2>
<p class="mb-2">Environment variables are the recommended way to change configuration settings when deploying the application as a Docker container.</p>
<p class="mb-2">To check if environment variables have been set, run this command from the shell in the container:</p>
<pre>env | grep -i '^config'</pre>

<h3 class="mt-2 mb-2 text-lg font-bold">Format</h3>
<p class="mb-2">Environment variables are defined in this format:</p>
<p class="font-mono">CONFIG_KEY1_KEY2_KEY3</p>

<h3 class="mt-2 mb-2 text-lg font-bold">Example - Nginx or Apache username</h3>
<pre>CONFIG_APP_HTTPUSER=www-data</pre>

<h3 class="mt-2 mb-2 text-lg font-bold">Example - MariaDB / MySQL</h3>
<pre>CONFIG_DATABASE_DRIVER=mysql
CONFIG_DATABASE_MYSQL_HOST=host
CONFIG_DATABASE_MYSQL_PORT=3306
CONFIG_DATABASE_MYSQL_SCHEMA=photon
CONFIG_DATABASE_MYSQL_USERNAME=username
CONFIG_DATABASE_MYSQL_PASSWORD=password</pre>

<h3 class="mt-2 mb-2 text-lg font-bold">Example - PostgeSQL</h3>
<pre>CONFIG_DATABASE_DRIVER=pgsql
CONFIG_DATABASE_PGSQL_HOST=host
CONFIG_DATABASE_PGSQL_PORT=5432
CONFIG_DATABASE_PGSQL_SCHEMA=photon
CONFIG_DATABASE_PGSQL_USERNAME=username
CONFIG_DATABASE_PGSQL_PASSWORD=password</pre>

<h3 class="mt-2 mb-2 text-lg font-bold">Example - SQLite</h3>
<pre>CONFIG_DATABASE_DRIVER=sqlite
CONFIG_DATABASE_SQLITE_PATH=/var/lib/photon/database/photon.db</pre>
