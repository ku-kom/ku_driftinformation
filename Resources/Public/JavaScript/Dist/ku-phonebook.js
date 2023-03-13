/**
 * Handle KU Driftinformation events
 */
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const input = document.getElementById('KuDriftinformation');
    const reset = document.getElementById('reset');
    const results = document.getElementById('ku-driftinformation-results');

    reset.addEventListener('click', () => {
        results.textContent = '';
        input.value = '';
        input.focus();
    });

});