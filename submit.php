<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["msg"];

    // Proteção contra ataques maliciosos
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $message = filter_var($message, FILTER_SANITIZE_STRING);

    // Validar reCAPTCHA (chave secreta)
    $recaptchaSecret = 'SUA_CHAVE_SECRETA_DO_RECAPTCHA';
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $recaptchaResult = json_decode($result);

    if ($recaptchaResult->success) {
        // Enviar o e-mail
        $to = "domingasduarte95@gmail.com";
        $subject = "New Contact Form Submission";
        $headers = "From: $email";
        $mailBody = "Name: $name\nEmail: $email\nMessage: $message";

        if (mail($to, $subject, $mailBody, $headers)) {
            header("Location: confirmation.html");
            exit;
        } else {
            echo "Message could not be sent.";
        }
    } else {
        echo "reCAPTCHA validation failed.";
    }
}
?>

