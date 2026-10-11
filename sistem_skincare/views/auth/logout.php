<?php
session_start();
session_unset();
session_destroy();

header("Location: /sistem_skincare/public/katalog.php");
exit;
?>