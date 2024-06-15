<?php
require 'vendor/autoload.php';
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
$mail = new PHPMailer(true);
 
try {
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                    // Servidor SMTP de Gmail
    $mail->SMTPAuth   = true;
    $mail->Username   = 'carlos.eduardo.ramos1997@gmail.com';               // Tu correo de Gmail
    $mail->Password   = 'Campos1997!';                     // Tu contraseña de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
 
    // Remitente y destinatario
    $mail->setFrom('carlos.eduardo.ramos1997@gmail.com', 'Carlos Campos');
    $mail->addAddress('destinatario@ejemplo.com', 'Nombre del Destinatario');     // Añadir un destinatario
 
    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Asunto del correo';
    $mail->Body    = 'Este es el <b>contenido</b> del correo en HTML.';
    $mail->AltBody = 'Este es el contenido del correo en texto plano para clientes que no soportan HTML.';
 
    $mail->send();
    echo 'El mensaje ha sido enviado';
} catch (Exception $e) {
    echo "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}";
}
?>