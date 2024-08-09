<?php

declare(strict_types=1);

use App\Functions\Output;

$title = 'About';
?>
<h1 class="pb-4 text-3xl font-bold">Photon</h1>

<div class="mt-2">
    <table class="text-left border-collapse">
        <tr>
            <th class="table-heading pr-6">Version</th>
            <td class="table-cell pr-6"><?= APP_VERSION ?></td>
        </tr>
        <tr>
            <th class="table-heading pr-6">Released</th>
            <td class="table-cell pr-6"><?= date("jS F Y", strtotime(APP_RELEASE)) ?></td>
        </tr>
        <tr>
            <th class="table-heading pr-6">Database</th>
            <td class="table-cell pr-6"><div class="flex"><?= Output::dbInfo(); ?></div></td>
        </tr>
        <tr>
            <th class="table-heading pr-6">PHP version</th>
            <td class="table-cell pr-6"><?= PHP_VERSION ?></td>
        </tr>
        <tr>
            <th class="table-heading pr-6">Docker</th>
            <td class="table-cell pr-6 underline text-blue-600 dark:text-red-600"><a href="<?= APP_DOCKER ?>" target="_blank"><?= APP_DOCKER ?></a></td>
        </tr>
        <tr>
            <th class="table-heading pr-6">GitHub</th>
            <td class="table-cell pr-6 underline text-blue-600 dark:text-red-600"><a href="<?= APP_GITHUB ?>" target="_blank"><?= APP_GITHUB ?></a></td>
        </tr>
    </table>
</div>

<div class="mt-10">
    <h2 class="text-2xl font-bold">Credits</h2>
    <p class="mt-4 mb-2">Photon is built using these software components:</p>
    <table class="text-left border-collapse">
        <tr>
            <th class="table-heading pr-6">Component</th>
            <th class="table-heading pr-6">Link</th>
        </tr>
        <tr>
            <td class="table-cell pr-6">Font Awesome</td>
            <td class="table-cell pr-6"><a class="underline text-blue-600 dark:text-red-600" href="https://fontawesome.com/" target="_blank">https://fontawesome.com/</a></td>
        </tr>
        <tr>
            <td class="table-cell pr-6">jQuery</td>
            <td class="table-cell pr-6"><a class="underline text-blue-600 dark:text-red-600" href="https://jquery.com/" target="_blank">https://jquery.com/</a></td>
        </tr>
        <tr>
            <td class="table-cell pr-6">Pretty-Print-JSON</td>
            <td class="table-cell pr-6"><a class="underline text-blue-600 dark:text-red-600" href="https://github.com/center-key/pretty-print-json" target="_blank">https://github.com/center-key/pretty-print-json</a></td>
        </tr>
        <tr>
            <td class="table-cell pr-6">Symfony HTTP Client</td>
            <td class="table-cell pr-6"><a class="underline text-blue-600 dark:text-red-600" href="https://github.com/symfony/http-client" target="_blank">https://github.com/symfony/http-client</a></td>
        </tr>
        <tr>
            <td class="table-cell pr-6">Tailwind CSS</td>
            <td class="table-cell pr-6"><a class="underline text-blue-600 dark:text-red-600" href="https://tailwindcss.com/" target="_blank">https://tailwindcss.com/</a></td>
        </tr>
    </table>
</div>

<div class="mt-10 w-[600px]">
    <h2 class="text-2xl font-bold">License</h2>

    <p class="mt-4">Photon is released under the MIT License.</p>
    <p class="mt-4">Copyright &copy; <?= date('Y') ?> Lee Chisnall.</p>

    <p class="mt-4">Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the &ldquo;Software&rdquo;), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:</p>

    <p class="mt-4">The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.</p>

    <p class="mt-4">THE SOFTWARE IS PROVIDED &ldquo;AS IS&rdquo;, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.</p>
</div>
