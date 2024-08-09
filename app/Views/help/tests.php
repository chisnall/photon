<?php

declare(strict_types=1);

$title = 'Help: Tests';
?>
<h2 class="mb-4 text-2xl font-bold">Tests</h2>

<h2 class="mt-6 mb-2 text-xl font-bold">Intro</h2>
<p class="mb-2">Testing is comprised of:</p>
<ol class="mb-2 list-decimal list-inside">
    <li>Unit tests on individual requests</li>
    <li>Group tests of multiple requests in sequence</li>
</ol>
<p class="mb-2">Unit tests are defined on the Tests tab in the request on the Home page.</p>
<p class="mb-2">Group tests are defined on the Tests page. The requests are run in sequence and the unit tests defined on the requests are run.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">Assertion Syntax</h2>
<p class="mb-2">For each unit test, an assertion is made to test the response output.</p>
<p class="mb-2">When a test value is a key-pair, use || as the key-pair separator. Examples are shown below.</p>
<p class="mb-2">Spaces are supported with the [[space]] placeholder. See the example below.</p>
<p class="mb-2">Header names are case-insensitive. Header values are case-sensitive.</p>
<p class="mb-2">JSON keys and values are both case-sensitive.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">Tests Available</h2>
<p class="mb-2">This is the current list of unit tests.</p>
<p class="mb-2">Tests that are not recognised will be skipped.</p>
<table class="mt-4 text-left border-collapse">
    <tr>
        <th class="table-heading pr-6">Test type</th>
        <th class="table-heading pr-6">Assertion value</th>
        <th class="table-heading pr-6">Example</th>
    </tr>
    <tr>
        <td class="table-cell pr-6">response.valid</td>
        <td class="table-cell pr-6">true/false</td>
        <td class="table-cell pr-6">true</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">response.code</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">200</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">response.scheme</td>
        <td class="table-cell pr-6">http/https</td>
        <td class="table-cell pr-6">https</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">response.time.less.than</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">50</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">response.time.greater.than</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">10</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">headers.count.equals</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">12</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">headers.header.present</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">date</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">headers.header.equals</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">content-type || application/json</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">headers.header.contains</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">cache-control || no-cache</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.valid</td>
        <td class="table-cell pr-6">true/false</td>
        <td class="table-cell pr-6">true</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.present</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">id</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.not.present</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">user</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.equals</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">id || 50</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.not.equals</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">user || 10</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.contains</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">created_at || 2024</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.not.contains</td>
        <td class="table-cell pr-6">key || value</td>
        <td class="table-cell pr-6">id || [[space]]</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.string</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">note</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.number</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">quantity</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.integer</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">count</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.float</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">weight</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.boolean</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">enabled</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.null</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">title</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.array</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">telephone</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.key.is.object</td>
        <td class="table-cell pr-6">key</td>
        <td class="table-cell pr-6">address</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.records.count.equals</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">5</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.records.count.less.than</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">10</td>
    </tr>
    <tr>
        <td class="table-cell pr-6">json.records.count.greater.than</td>
        <td class="table-cell pr-6">integer</td>
        <td class="table-cell pr-6">2</td>
    </tr>

</table>
