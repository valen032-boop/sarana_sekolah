<?php
require_once __DIR__ . '/config/auth.php';
if (!isLogin()) redirect('login.php');
redirect(user()['role'] === 'admin' ? 'admin/dashboard.php' : 'murid/dashboard.php');
