<?php

declare(strict_types=1);

/**
 * Esta parte do código é o núcleo de apoio do projeto.
 * Ela não mostra a tela, mas organiza regras que aparecem em várias páginas,
 * como validação, limpeza de dados e controle do tema.
 *
 * Em PHP, uma função é como um bloco de instruções com nome.
 * Quando a gente chama a função, o PHP executa tudo que está dentro dela.
 * Isso evita repetir código em várias páginas e deixa o projeto mais limpo.
 */

/**
 * normalizarTema()
 *
 * Garante que o tema recebido seja sempre um valor válido.
 * O sistema só aceita 'claro' ou 'escuro'.
 *
 * Por que isso importa?
 * Porque o navegador pode mandar qualquer string. Se a gente não validar,
 * uma palavra errada poderia quebrar a lógica do CSS ou mostrar o site sem padrão.
 */
function normalizarTema(string $tema): string
{
    return in_array($tema, ['claro', 'escuro'], true) ? $tema : 'claro';
}

/**
 * obterTemaPreferido()
 *
 * Lê o valor salvo no cookie chamado 'tema'.
 * O cookie fica no navegador do usuário e lembra a escolha dele.
 *
 * Se não houver cookie, usa 'claro' como padrão.
 * Isso faz o site abrir sempre em uma aparência segura, sem quebrar.
 */
function obterTemaPreferido(): string
{
    $temaSalvo = $_COOKIE['tema'] ?? 'claro';
    return normalizarTema((string) $temaSalvo);
}

/**
 * salvarTemaPreferido()
 *
 * Salva o tema em cookie e também em sessão.
 *
 * O cookie serve para manter a escolha mesmo depois que o usuário fecha o navegador.
 * A sessão serve para manter essa escolha na navegação atual.
 *
 * A função usa setcookie() para guardar a informação no navegador.
 * O PHP precisa do tempo de expiração para dizer por quanto tempo esse dado fica salvo.
 */
function salvarTemaPreferido(string $tema): void
{
    $temaNormalizado = normalizarTema($tema);
    setcookie('tema', $temaNormalizado, time() + (86400 * 30), '/');
    $_SESSION['tema'] = $temaNormalizado;
}

/**
 * limparTexto()
 *
 * Remove espaços no começo e no fim da string.
 *
 * Exemplo:
 * '  Ana  ' vira 'Ana'
 *
 * Isso evita que o usuário envie dados com espaços extras que podem confundir a lógica.
 */
function limparTexto(mixed $valor): string
{
    return trim((string) ($valor ?? ''));
}

/**
 * limparInteiroOuNulo()
 *
 * Tenta transformar qualquer valor em inteiro.
 * Se o valor vier vazio ou não for um inteiro válido, retorna null.
 *
 * Esse padrão é útil porque idade, quantidade, número de itens etc.
 * normalmente precisam ser inteiros.
 */
function limparInteiroOuNulo(mixed $valor): ?int
{
    if ($valor === null || trim((string) $valor) === '') {
        return null;
    }

    $valorConvertido = filter_var(trim((string) $valor), FILTER_VALIDATE_INT);
    return $valorConvertido !== false ? (int) $valorConvertido : null;
}

/**
 * limparDecimalOuNulo()
 *
 * Faz a mesma coisa que a função anterior, mas para números decimais.
 *
 * Isso serve para peso e altura, que podem ter casas decimais como 60.5 ou 1.75.
 */
function limparDecimalOuNulo(mixed $valor): ?float
{
    if ($valor === null || trim((string) $valor) === '') {
        return null;
    }

    $valorConvertido = filter_var(trim((string) $valor), FILTER_VALIDATE_FLOAT);
    return $valorConvertido !== false ? (float) $valorConvertido : null;
}

/**
 * validarDadosPessoa()
 *
 * Esta função é a regra principal do projeto.
 * Ela pega os dados do formulário, limpa cada campo e verifica se estão corretos.
 *
 * O retorno contém:
 * - 'ok': true ou false
 * - 'errors': lista de mensagens de erro
 * - 'data': array já pronto para salvar
 *
 * Isso é importante porque o PHP pode receber qualquer coisa do navegador.
 * A validação impede que dados inválidos entrem no sistema.
 */
function validarDadosPessoa(array $dados): array
{
    $listaErros = [];
    $nome = limparTexto($dados['nome'] ?? '');
    $idade = limparInteiroOuNulo($dados['idade'] ?? null);
    $peso = limparDecimalOuNulo($dados['peso'] ?? null);
    $altura = limparDecimalOuNulo($dados['altura'] ?? null);

    if ($nome === '') {
        $listaErros[] = 'Digite o nome.';
    } elseif (strlen($nome) < 2) {
        $listaErros[] = 'O nome deve ter pelo menos 2 caracteres.';
    }

    if ($idade === null || $idade < 0 || $idade > 120) {
        $listaErros[] = 'A idade deve ser um número inteiro entre 0 e 120.';
    }

    if ($peso === null || $peso <= 0 || $peso > 500) {
        $listaErros[] = 'O peso deve ser maior que 0 e menor que 500.';
    }

    if ($altura === null || $altura <= 0 || $altura > 3) {
        $listaErros[] = 'A altura deve ser maior que 0 e menor que 3 metros.';
    }

    return [
        'ok' => empty($listaErros),
        'errors' => $listaErros,
        'data' => [
            'nome' => $nome,
            'idade' => $idade ?? 0,
            'peso' => $peso ?? 0.0,
            'altura' => $altura ?? 0.0,
        ],
    ];
}

/**
 * montarDadosPessoa()
 *
 * Depois de validar, essa função organiza os dados em um array limpo.
 *
 * Se os dados forem inválidos, ela devolve valores neutros para evitar quebrar a tela.
 * Isso protege a página de cair em erro se alguém mandar algo inesperado.
 */
function montarDadosPessoa(array $dados): array
{
    $resultadoValidacao = validarDadosPessoa($dados);

    if (!$resultadoValidacao['ok']) {
        return [
            'nome' => limparTexto($dados['nome'] ?? ''),
            'idade' => 0,
            'peso' => 0.0,
            'altura' => 0.0,
        ];
    }

    return $resultadoValidacao['data'];
}
