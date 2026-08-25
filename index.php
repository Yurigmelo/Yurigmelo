<?php

// declare(strict_types=1); faz com que o PHP trate os tipos de dados de forma mais rígida.
// Isso significa que, se uma função ou variável esperar um inteiro e receber outra coisa,
// o PHP pode gerar erro em vez de converter automaticamente o valor sem aviso.
//
// Por exemplo: se o código esperar um número e receber texto, isso pode quebrar logo no início.
// Com strict_types ativado, o comportamento fica mais previsível e ajuda a evitar bugs.
//
// Em projetos pequenos, isso é muito útil porque deixa o código mais seguro e fácil de manter.
// Mas é importante lembrar que ele só vale para as funções que recebem parâmetros tipados.
declare(strict_types=1);

// session_start() é o comando que ativa a sessão do PHP.
// A sessão funciona como uma memória temporária para esse usuário.
// Aqui ela é usada para guardar os dados da primeira pessoa antes de redirecionar.
session_start();

// require_once inclui o arquivo app.php uma única vez.
// Ele carrega as funções de validação e tema que serão usadas nesta página.
require_once __DIR__ . '/app.php';

// Ao abrir a página, o sistema tenta pegar o tema já salvo no cookie do navegador.
// Isso evita que o site volte para o tema padrão toda vez que a tela recarregar.
$temaSelecionado = obterTemaPreferido();

// Lista de erros que pode aparecer se o usuário mandar algo inválido.
$listaErros = [];

// Os campos do formulário começam vazios para a tela ser carregada em estado neutro.
$dadosFormulario = [
    'nome' => '',
    'idade' => '',
    'peso' => '',
    'altura' => '',
];

// $_SERVER['REQUEST_METHOD'] verifica qual tipo de requisição chegou.
// Se for POST, significa que o formulário foi enviado pela página mesmo.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega os valores enviados pelo formulário e salva em um array organizado.
    $dadosFormulario = [
        'nome' => $_POST['nome'] ?? '',
        'idade' => $_POST['idade'] ?? '',
        'peso' => $_POST['peso'] ?? '',
        'altura' => $_POST['altura'] ?? '',
    ];

    // Chama a função de validação centralizada no app.php.
    // Ela retorna se os dados estão corretos e também as mensagens de erro.
    $resultadoValidacao = validarDadosPessoa($dadosFormulario);

    // Se a validação deu certo, salva os dados na sessão e vai para a próxima etapa.
    if ($resultadoValidacao['ok']) {
        $_SESSION['pessoa1'] = $resultadoValidacao['data'];

        // Pega o tema enviado no formulário e normaliza o valor.
        $temaSelecionado = normalizarTema((string) ($_POST['tema'] ?? 'claro'));
        salvarTemaPreferido($temaSelecionado);

        // header('Location: ...') redireciona o usuário para outra página.
        // O exit faz o script parar imediatamente para não continuar a execução.
        header('Location: pagina2.php');
        exit;
    }

    // Se a validação falhou, os erros são guardados e a mesma página continua exibindo them.
    $listaErros = $resultadoValidacao['errors'];
    $temaSelecionado = normalizarTema((string) ($_POST['tema'] ?? $temaSelecionado));
    salvarTemaPreferido($temaSelecionado);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primeira pessoa</title>
    <style>
        :root {
            --bg: <?= $temaSelecionado === 'escuro' ? '#171a1a' : '#f3f3f1'; ?>;
            --card: <?= $temaSelecionado === 'escuro' ? '#202522' : '#ffffff'; ?>;
            --line: <?= $temaSelecionado === 'escuro' ? '#343d39' : '#dfe3df'; ?>;
            --text: <?= $temaSelecionado === 'escuro' ? '#f4f4f4' : '#1f2924'; ?>;
            --muted: <?= $temaSelecionado === 'escuro' ? '#c8d0cb' : '#5d6460'; ?>;
            --accent: #d66d42;
            --error-bg: <?= $temaSelecionado === 'escuro' ? '#2b1e1d' : '#fff0ee'; ?>;
            --error-text: <?= $temaSelecionado === 'escuro' ? '#f8c9c3' : '#a63a2c'; ?>;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, sans-serif;
        }

        .page {
            max-width: 540px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 26px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 2rem;
            line-height: 1.1;
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.6;
        }

        .error {
            margin-bottom: 16px;
            border: 1px solid var(--line);
            background: var(--error-bg);
            color: var(--error-text);
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        label {
            display: block;
            margin-bottom: 16px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        input, select {
            width: 100%;
            margin-top: 7px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: transparent;
            color: var(--text);
            font-size: 1rem;
        }

        input:focus, select:focus {
            outline: 2px solid rgba(214, 109, 66, 0.18);
            border-color: var(--accent);
        }

        button {
            width: 100%;
            margin-top: 8px;
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            padding: 14px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .small {
            font-size: 12px;
            margin-top: 12px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Primeira pessoa</h1>
            <p>Preencha os dados e continue para a segunda etapa.</p>

            <?php if (!empty($listaErros)): ?>
                <div class="error">
                    <?php foreach ($listaErros as $erro): ?>
                        <div><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label>
                    Nome
                    <input type="text" name="nome" value="<?= htmlspecialchars($dadosFormulario['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label>
                    Idade
                    <input type="number" name="idade" min="0" max="120" step="1" value="<?= htmlspecialchars((string) $dadosFormulario['idade'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label>
                    Peso (kg)
                    <input type="number" name="peso" min="0" step="0.1" value="<?= htmlspecialchars((string) $dadosFormulario['peso'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label>
                    Altura (m)
                    <input type="number" name="altura" min="0" step="0.01" value="<?= htmlspecialchars((string) $dadosFormulario['altura'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </label>

                <label>
                    Tema
                    <select name="tema">
                        <option value="claro" <?= $temaSelecionado === 'claro' ? 'selected' : ''; ?>>Claro</option>
                        <option value="escuro" <?= $temaSelecionado === 'escuro' ? 'selected' : ''; ?>>Escuro</option>
                    </select>
                </label>

                <button type="submit">Continuar</button>
            </form>
        </div>
    </div>
</body>
</html>
