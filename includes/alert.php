<?php
// Flash messages
function display_flash($key, $type = 'danger') {
    if (isset($_SESSION[$key])) {
        // Determine alert class based on type
        $alert_class = $type === 'success' ? 'alert-success' : ($type === 'info' ? 'alert-info' : 'alert-danger');
        echo "
        <div class='alert {$alert_class} alert-dismissible fade show my-3' role='alert'>
            <strong>{$_SESSION[$key]}</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        unset($_SESSION[$key]);
    }
}

// Display danger message
display_flash('message', 'danger');

// Display success message
display_flash('success', 'success');
