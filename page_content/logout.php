<?php
require_once('../boot.php');
unset($_SESSION['user']);
header('Location: /index.php');