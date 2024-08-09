<?php
/** @var array $classNameShort */
/** @var array $exceptionFile */
/** @var array $exceptionLine */
/** @var array $exceptionMessage */
/** @var array $exceptionPreviousMessage */
/** @var array $exceptionTrace */
/** @var array $exceptionTraceString */

declare(strict_types=1);

use App\Functions\Output;
?>

<div class="flex justify-center">
    <div class="w-[1000px] mt-10 rounded-xl border bg-zinc-50 border-zinc-300 dark:bg-zinc-900 dark:border-zinc-650">
        <div>

            <div class="flex justify-between ml-3 mr-1 mt-2">
                <div class="flex">
                    <div class="mr-10 text-zinc-400 dark:text-zinc-400">Photon <?= APP_VERSION ?></div>
                </div>
                <div>
                    <button type="button" id="theme-toggle" class="block px-2 py-0 m-0 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                        <?= Output::icon('theme-toggle') ?>
                    </button>
                </div>
            </div>

            <div class="flex pl-10 pt-6">
                <div class="w-[38px]">
                    <?= Output::icon('warning') ?>
                </div>
                <div>
                    <h1 class="inline-block ml-3 text-3xl font-bold"><?= $classNameShort ?></h1>
                </div>
            </div>

            <div class="p-10">

                <div class="text-lg font-bold">
<?php if ($exceptionMessage): ?>
                        <div class="mb-2 text-red-600 dark:text-orange-400"><?= $exceptionMessage ?></div>
<?php endif; ?>
<?php if ($exceptionPreviousMessage): ?>
                        <div class="mb-2 text-red-600 dark:text-orange-400"><?= $exceptionPreviousMessage ?></div>
<?php endif; ?>
                </div>

                <div class="mt-8">
                    <table class="w-full text-left border-collapse">
                        <tr>
                            <th class="table-heading">File</th>
                            <td class="table-cell font-mono"><?= $exceptionFile ?></td>
                        </tr>
                        <tr>
                            <th class="table-heading">Line</th>
                            <td class="table-cell font-mono"><?= $exceptionLine ?></td>
                        </tr>
                    </table>
                </div>

                <div class="mt-8 text-sm">
                    <table class="w-full text-left">
                        <tr>
                            <th class="table-heading">File</th>
                            <th class="table-heading">Line</th>
                            <th class="table-heading">Class</th>
                            <th class="table-heading">Method</th>
                        </tr>
<?php
# init PDO information to stop warnings
$tracePdo = null;

// Process trace array
foreach ($exceptionTrace as $traceArray) {
    // Get trace details
    $traceFile = $traceArray['file'] ?? null;
    $traceLine = $traceArray['line'] ?? '-';
    $traceFunction = $traceArray['function'] ?? null;
    $traceClass = $traceArray['class'] ?? '[function]';
    $traceType = $traceArray['type'] ?? null;
    $traceArgs = $traceArray['args'] ?? null;

    // Set file class
    if ($traceFile) {
        $traceFileClass = ' font-mono';
    } else {
        $traceFileClass = null;
        $traceFile = '[internal function]';
    }

    // Check for SQL
    if ($traceClass == "PDO") {
        $tracePdo = $traceArgs[0] ?? null;
    }

    // Output row
    echo "                        <tr>\n";
    echo "                            <td class=\"table-cell$traceFileClass\">$traceFile</td>\n";
    echo "                            <td class=\"table-cell text-right\">$traceLine</td>\n";
    echo "                            <td class=\"table-cell\">$traceClass</td>\n";
    echo "                            <td class=\"table-cell\">$traceFunction</td>\n";
    echo "                        </tr>\n";
}
?>
                    </table>
                </div>
<?php
// Check for PDO
if ($tracePdo)
    {
    echo "                <div class=\"mt-8 text-sm\">\n";
    echo "                    <table class=\"w-full text-left\">\n";
    echo "                        <tr>\n";
    echo "                            <th class=\"table-heading\">PDO</th>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                            <td class=\"table-cell font-mono\">$tracePdo</td>\n";
    echo "                        </tr>\n";
    echo "                    </table>\n";
    echo "                </div>\n";
    }
?>
                <div>
                    <pre class="hidden mt-4 text-sm whitespace-pre-wrap"><?= $exceptionTraceString ?></pre>
                </div>

                <div class="mt-8 text-sm">
                    <div class="px-2 py-1 font-bold font-mono border-t border-l border-r border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">
                        <?= $exceptionFile ?>
                    </div>
                    <div class="font-mono border border-zinc-300 dark:border-zinc-650">
<?php
$fileRange = 6;
$fileCounter = 1;
$fileArray = file($exceptionFile);
$fileCapture = null;
foreach ($fileArray as $fileLine) {
    if ($fileCounter >= ($exceptionLine - $fileRange) && $fileCounter <= ($exceptionLine + $fileRange)) {
        $fileLine = rtrim($fileLine);
        echo "                        <div class=\"flex\">\n";
        echo "                            <div class=\"flex-none min-w-16 pl-3 pr-5 text-right text-zinc-400 dark:text-zinc-500 border-r border-zinc-300 dark:border-zinc-650\">$fileCounter</div>\n";
        echo "                            <pre class=\"flex w-full pl-5 pr-5 whitespace-pre-wrap";
        if ( $fileCounter == $exceptionLine) {
            echo " bg-amber-200 dark:bg-amber-800";
        }
        echo "\">$fileLine</pre>\n";
        echo "                        </div>\n";
    }
    $fileCounter++;
}
?>
                    </div>
                </div>

            </div>

            <div class="ml-3 mb-2">
                <div class="flex">
                    <div class="mr-2 text-zinc-400 dark:text-zinc-400">Support: </div>
                    <div class="underline text-blue-600 dark:text-red-600"><a href="<?= APP_SUPPORT ?>" target="_blank"><?= APP_SUPPORT ?></div>
                </div>
            </div>

        </div>
    </div>
</div>
