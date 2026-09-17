<?php
require dirname(__DIR__) . '/app/bootstrap.php';

if (is_post()) {
    verify_csrf();
    end_user_session('admin');
    flash('ok', 'Sesión cerrada.');
}
redirect('admin/login.php');
