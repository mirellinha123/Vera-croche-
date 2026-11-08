<?php
header('Content-Type: application/json');

// Validar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Coleta e valida dados
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$assunto = isset($_POST['assunto']) ? trim($_POST['assunto']) : '';
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';

// Validações
$errors = [];

if (empty($nome) || strlen($nome) < 3) {
    $errors[] = 'Nome deve ter no mínimo 3 caracteres';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido';
}

if (empty($assunto)) {
    $errors[] = 'Assunto é obrigatório';
}

if (empty($mensagem) || strlen($mensagem) < 10) {
    $errors[] = 'Mensagem deve ter no mínimo 10 caracteres';
}

// Se houver erros, retorna JSON
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Erro na validação',
        'errors' => $errors
    ]);
    exit;
}

// Sanitiza dados
$nome = htmlspecialchars($nome);
$email = htmlspecialchars($email);
$telefone = htmlspecialchars($telefone);
$assunto = htmlspecialchars($assunto);
$mensagem = htmlspecialchars($mensagem);

// Email do destinatário
$destinatario = 'seu_email@exemplo.com'; // ALTERE PARA SEU EMAIL

// Assunto do email
$titulo = "Novo contato do site - " . $assunto;

// Corpo do email
$corpo = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #8B7D7B; }
        .container { background-color: #f9f9f9; padding: 20px; border-radius: 10px; }
        .header { background-color: #FFB7D5; color: white; padding: 15px; border-radius: 5px; }
        .campo { margin: 15px 0; padding: 10px; background-color: white; border-left: 4px solid #B19CD9; }
        .label { font-weight: bold; color: #B19CD9; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>🧶 Novo Contato - Arte do Crochê</h2>
        </div>
        
        <div class='campo'>
            <span class='label'>Nome:</span><br>
            $nome
        </div>
        
        <div class='campo'>
            <span class='label'>Email:</span><br>
            <a href='mailto:$email'>$email</a>
        </div>
        
        <div class='campo'>
            <span class='label'>Telefone:</span><br>
            " . (!empty($telefone) ? $telefone : 'Não informado') . "
        </div>
        
        <div class='campo'>
            <span class='label'>Assunto:</span><br>
            $assunto
        </div>
        
        <div class='campo'>
            <span class='label'>Mensagem:</span><br>
            " . nl2br($mensagem) . "
        </div>
        
        <div style='margin-top: 30px; font-size: 12px; color: #999;'>
            <p>Este é um email automático. Não responda diretamente. Responda ao email do visitante se desejar entrar em contato.</p>
        </div>
    </div>
</body>
</html>
";

// Headers do email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: $email" . "\r\n";
$headers .= "Reply-To: $email" . "\r\n";

// Envia o email
$sucesso = mail($destinatario, $titulo, $corpo, $headers);

// Log da tentativa (opcional)
$log = "[" . date('Y-m-d H:i:s') . "] " . $nome . " (" . $email . ") - " . ($sucesso ? "ENVIADO" : "ERRO") . "\n";
file_put_contents('logs/contatos.log', $log, FILE_APPEND);

// Retorna resposta JSON
if ($sucesso) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Obrigada por entrar em contato.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao enviar a mensagem. Tente novamente mais tarde.'
    ]);
}
?>