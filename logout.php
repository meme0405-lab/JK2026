<?php
session_start();

// Destruir a sessão
session_destroy();

// Redirecionar para login
header("Location: index.html");
exit();

?>
