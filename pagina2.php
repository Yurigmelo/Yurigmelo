<?php

declare(strict_types=1);

// A segunda página também precisa da sessão ativa.
// Sem isso, o PHP não consegue ler os dados que foram salvos na primeira página.
session_start();
require_once __DIR__ . '/app.php';

// Aqui o sistema lê o tema salvo no navegador e os dados que vieram da página anterior.
$temaSelecionado = obterTemaPreferido();
$primeiraPessoa = $_SESSION['pessoa1'] ?? [];
$segundaPessoa = $_SESSION['pessoa2'] ?? [];
$listaErros = [];

// O formulário também começa vazio para garantir que a página carregue em estado inicial.
$dadosFormulario = [
    'nome' => '',
    'idade' => '',
    'peso' => '',
    'altura' => '',
];

// Se esta página receber um POST, significa que o usuário acabou de enviar os dados da segunda pessoa.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletamos os dados enviados e os organizamos em um formato fácil de manipular.
    $dadosFormulario = [
        'nome' => $_POST['nome'] ?? '',
        'idade' => $_POST['idade'] ?? '',
        'peso' => $_POST['peso'] ?? '',
        'altura' => $_POST['altura'] ?? '',
    ];

    // Chama a mesma validação usada na primeira página.
    $resultadoValidacao = validarDadosPessoa($dadosFormulario);

    // Se tudo estiver correto, salva a segunda pessoa na sessão.
    if ($resultadoValidacao['ok']) {
        $_SESSION['pessoa2'] = $resultadoValidacao['data'];
        $temaSelecionado = normalizarTema((string) ($_POST['tema'] ?? 'claro'));
        salvarTemaPreferido($temaSelecionado);
        $segundaPessoa = $_SESSION['pessoa2'];
    } else {
        // Se tiver erro, a página continua aberta e mostra mensagens de feedback.
        $listaErros = $resultadoValidacao['errors'];
        $temaSelecionado = normalizarTema((string) ($_POST['tema'] ?? $temaSelecionado));
        salvarTemaPreferido($temaSelecionado);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segunda pessoa</title>
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

        .summary {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
        }

        .summary h3 {
            margin: 0 0 16px;
            font-size: 1.2rem;
        }

        .box {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .box h4 {
            margin: 0 0 10px;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .box p {
            margin: 6px 0;
            font-size: 0.95rem;
        }

        strong {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Segunda pessoa</h1>
            <p>Complete o cadastro e veja o resumo final.</p>

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

                <button type="submit">Salvar</button>
            </form>

            <?php if (!empty($segundaPessoa)): ?>
                <div class="summary">
                    <h3>Resumo</h3>

                    <div class="box">
                        <h4>Primeira pessoa</h4>
                        <?php foreach ($primeiraPessoa as $campo => $valor): ?>
                            <p><strong><?= ucfirst((string) $campo); ?></strong>: <?= htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div class="box">
                        <h4>Segunda pessoa</h4>
                        <?php foreach ($segundaPessoa as $campo => $valor): ?>
                            <p><strong><?= ucfirst((string) $campo); ?></strong>: <?= htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
