"use strict";

if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
    document.getElementById("html").setAttribute("data-theme", "dark");
} else {
    document.documentElement.classList.remove('dark')
    document.getElementById("html").setAttribute("data-theme", "light");
}
