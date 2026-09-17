<?php
require dirname(__DIR__) . '/app/bootstrap.php';

if (is_post()) {
    verify_csrf();
    end_user_session('client');
    flash('ok', 'Cerraste sesión correctamente.');
}
redirect('portal/login.php');
