import './bootstrap.js';

import './styles/bootstrap_template/core.css';
import './styles/bootstrap_template/theme-default.css';
import './styles/bootstrap_template/demo.css';
import './styles/bootstrap_template/perfect-scrollbar.css';
import './styles/profile_picture.css';
import './styles/app.css';

import '@fortawesome/fontawesome-free/css/all.css';
import '@fortawesome/fontawesome-free/css/v4-shims.css';

import $ from 'jquery';
window.$ = window.jQuery = $;

import * as bootstrap from 'bootstrap';

import './js/bootstrap_template/config.js';
import './js/bootstrap_template/helpers.js';
import './js/bootstrap_template/perfect-scrollbar.js';
import './js/bootstrap_template/menu.js';
import './js/bootstrap_template/main.js';
import './js/bootstrap_template/template-customizer.js';


$(document).ready(function(){
    // Init tooltip
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
