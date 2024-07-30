"use strict";

function saveSession() {
    // Get AJAX token
    let ajaxToken = $("input#ajaxToken");

    // Token is not present on the Login page or error page
    if (ajaxToken.length) {
        // Save setting in session on clicking the theme toggle buttons
        $.ajax({ method: "POST", url: "/ajax.php", data: {token: ajaxToken.val(), key: 'page/theme', value: localStorage.getItem('color-theme')} });
    }
}

// Get icon IDs
let themeTogglePlaceholder = document.getElementById('theme-toggle-placeholder');
let themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
let themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

// Hide the placeholder icon
themeTogglePlaceholder.classList.add('hidden');

// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) &&
    window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    themeToggleLightIcon.classList.remove('hidden');
} else {
    themeToggleDarkIcon.classList.remove('hidden');
}

// Get button ID
let themeToggleBtn = document.getElementById('theme-toggle');

// Event listener for the button
themeToggleBtn.addEventListener('click', function() {
    // Toggle icons inside button
    themeToggleDarkIcon.classList.toggle('hidden');
    themeToggleLightIcon.classList.toggle('hidden');

    // If set via local storage previously
    if (localStorage.getItem('color-theme')) {
        if (localStorage.getItem('color-theme') === 'light') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
            document.getElementById("html").setAttribute("data-theme", "dark");
        } else {
              document.documentElement.classList.remove('dark');
              localStorage.setItem('color-theme', 'light');
              document.getElementById("html").setAttribute("data-theme", "light");
        }
    // If NOT set via local storage previously
    } else {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
            document.getElementById("html").setAttribute("data-theme", "light");
        } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
        document.getElementById("html").setAttribute("data-theme", "dark");
        }
    }

    // Save to session
    saveSession();
});

// Save to session
saveSession();
