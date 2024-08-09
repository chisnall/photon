<?php

declare(strict_types=1);

$title = 'Help: Variables';
?>
<h2 class="mb-4 text-2xl font-bold">Variables</h2>

<h2 class="mt-6 mb-2 text-xl font-bold">Intro</h2>
<p class="mb-2">Variables are supported in requests on the Home page.</p>
<p class="mb-2">Variables and their type are shown in the Variables list on the Home page.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">Variable Types</h2>
<p class="mb-2">Three types of variables are supported.</p>
<table class="mt-4 text-left border-collapse">
    <tr>
        <th class="table-heading pr-6">Type</th>
        <th class="table-heading pr-6">Scope</th>
        <th class="table-heading pr-6">Priority</th>
        <th class="table-heading pr-6">Notes</th>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6">Global</td>
        <td class="table-cell pr-6">Global</td>
        <td class="table-cell pr-6">3rd</td>
        <td class="table-cell pr-6">
            <p>Static variables.</p>
            <p>Stored in user settings.</p>
            <p>Manage these variables in Settings <i class="fa-solid fa-arrow-right-long"></i> Variables.</p>
        </td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6">Collection</td>
        <td class="table-cell pr-6">Collection</td>
        <td class="table-cell pr-6">2nd</td>
        <td class="table-cell pr-6">
            <p>Static variables.</p>
            <p>Stored in the collection record.</p>
            <p>Manage these variables by editing a collection.</p>
        </td>
    </tr>
    <tr class="align-top">
        <td class="table-cell pr-6">Request</td>
        <td class="table-cell pr-6">Collection</td>
        <td class="table-cell pr-6">1st</td>
        <td class="table-cell pr-6">
            <p>Request variables are values captured from a request in the JSON response data.</p>
            <p>Any key value in the JSON response can be stored as a request variable.</p>
            <p>Request variables can then be used in subsequent requests in the same collection.</p>
            <p>Manage these variables in the Variables tab in the request section of the Home page.</p>
            <p class="mb-2">These variables are session based and last until logout.</p>
        </td>
    </tr>
</table>

<h2 class="mt-6 mb-2 text-xl font-bold">Usage</h2>
<p class="mb-2">Variables can we used in all inputs in the request section of the Home page.</p>
<p class="mb-2">Variables can also be used in test assertions.</p>
<p class="mb-2">Variable names are unique and can only store one value at a time.</p>
<p>If variable names clash, the priority is as follows:</p>
<ol class="list-decimal list-inside">
    <li>Request</li>
    <li>Collection</li>
    <li>Global</li>
</ol>

<h2 class="mt-6 mb-2 text-xl font-bold">Syntax</h2>
<p class="mb-2">Use <span class="font-mono">{{X}}</span> syntax to reference variables.</p>
<p class="mb-2">Multiple placeholders can be used in the same input.</p>
<p class="mb-2">You can include global, collection and request variables in the same input.</p>
<p>Example - 2 variables in request URL:</p>
<p class="mb-2 font-mono">{{base_url}}/api/notes/{{id_of_note}}</p>
<p>Example - variable in request body:</p>
<p class="mb-2 font-mono">{ "user": {{user_id}} }</p>
<p class="mb-2">Variables are case sensitive.</p>
<p class="mb-2">If the variable value does not exist, the <span class="font-mono">{{X}}</span> placeholder will be sent &ldquo;as is&rdquo; in the request.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">JSON Arrays</h2>
<p class="mb-2">For response data that returns an array of objects, only the first array element will be processed for request variables.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">Reset Variables</h2>
<p class="mb-2">Request variables can be reset.</p>
<p class="mb-2">Use the <i class="fa-solid fa-arrow-rotate-left"></i> icon to reset the value of the request variable.</p>

<h2 class="mt-6 mb-2 text-xl font-bold">Inspect Variables</h2>
<p class="mb-2">Some variable values are very long and will be displayed as the first 50 characters.</p>
<p class="mb-2">This is indicated by the variable value being suffixed with ...</p>
<p class="mb-2">Use the <i class="fa-solid fa-magnifying-glass"></i> icon to inspect the full variable value.</p>
