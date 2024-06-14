<?php
// config.php

// Establecer la política de seguridad de contenido (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://apis.google.com");
?>
