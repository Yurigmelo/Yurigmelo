# Yurigmelo

## Visão geral

Este projeto é uma pequena aplicação em PHP que faz um cadastro em duas etapas. A ideia é simples: a primeira pessoa preenche os seus dados, esses dados são guardados na sessão e a segunda página carrega o restante do cadastro. No final, as duas pessoas aparecem em um resumo.

A estrutura foi pensada para mostrar de forma fácil como funciona:

- formulário HTML
- processamento em PHP
- uso de `$_POST`
- uso de `$_SESSION`
- uso de `setcookie()`
- validação de dados
- manutenção do mesmo tema visual em todas as páginas

---

## Estrutura do projeto

Os arquivos principais são:

- `index.php` — primeira etapa do formulário
- `pagina2.php` — segunda etapa e resumo final
- `app.php` — funções auxiliares para validação e tema
- `README.md` — explicação do projeto

O projeto foi mantido simples e com o CSS dentro do próprio PHP para facilitar a leitura e seguir o estilo pedido.

---

## Fluxo do sistema

### 1. O usuário entra na página inicial

Quando alguém acessa `index.php`, o PHP executa algumas coisas antes de mostrar a página:

- inicia a sessão com `session_start()`;
- carrega as funções úteis de `app.php`;
- lê o tema já guardado no cookie;
- prepara os campos vazios do formulário.

Isso acontece porque a página precisa saber se o usuário já escolheu um tema e também precisa manter os dados do cadastro em andamento.

### 2. O usuário envia o formulário

Quando o botão é apertado, o navegador envia os dados por `POST`. O PHP recebe isso assim:

```php
$_POST['nome']
$_POST['idade']
$_POST['peso']
$_POST['altura']
```

Esses dados chegam em um array interno do PHP. A partir daí, o código valida cada campo, limpa o texto e só salva quando tudo estiver correto.

### 3. O sistema salva a primeira pessoa

Se os dados estiverem bons, o código faz isso:

```php
$_SESSION['pessoa1'] = $resultadoValidacao['data'];
```

Essa linha salva a primeira pessoa na sessão. A sessão funciona como um armazenamento temporário do navegador para a mesma navegação. Ela não fica permanente como banco de dados, mas mantém os dados enquanto a pessoa ainda estiver navegando no projeto.

### 4. O código redireciona para a segunda página

Depois do cadastro da primeira pessoa, o sistema executa:

```php
header('Location: pagina2.php');
exit;
```

O `header()` manda o navegador para outra página e o `exit` interrompe a execução da página atual, evitando que o script continue em loop ou rode coisa duplicada.

### 5. A segunda página pega os dados da primeira pessoa

Na segunda página, o PHP faz:

```php
$primeiraPessoa = $_SESSION['pessoa1'] ?? [];
```

Ou seja: ele tenta encontrar os dados da primeira pessoa na sessão. Se não existir, usa um array vazio para não quebrar o código.

### 6. A segunda pessoa é validada e salva

A lógica da segunda página é quase igual à primeira. O usuário envia os dados pela segunda vez e o PHP valida novamente. Caso tudo esteja correto:

```php
$_SESSION['pessoa2'] = $resultadoValidacao['data'];
```

Assim, a segunda pessoa também fica guardada em sessão.

### 7. O resumo final é exibido

Depois, o código lê os dois arrays da sessão e mostra os dados em um resumo simples:

- primeira pessoa
- segunda pessoa

Isso deixa o projeto fácil de entender e bem visualmente direto.

---

## Dúvidas comuns sobre as principais funções

### `session_start()`

Essa função inicia a sessão do PHP. Ela é essencial para guardar dados entre páginas.

Por exemplo: sem ela, `$_SESSION` não funcionaria.

### `$_POST`

`$_POST` é uma variável especial do PHP que guarda dados enviados por formulário via método POST.

Isso significa que, quando o usuário envia o formulário, o PHP recebe os valores e pode tratá-los.

### `$_SESSION`

`$_SESSION` guarda dados temporariamente para a mesma sessão do usuário.

No projeto, ele salva a primeira e a segunda pessoa para que a segunda página consiga acessar os dados da primeira.

### `setcookie()`

`setcookie()` salva uma informação no navegador do usuário.

No projeto, ele salva o tema escolhido (`claro` ou `escuro`) por 30 dias. Isso faz com que, ao voltar ao site, a pessoa veja a mesma aparência que escolheu antes.

### `header('Location: pagina2.php')`

Essa função envia o navegador para outra página. É a forma mais comum de redirecionar em PHP.

No projeto, ela é usada para levar o usuário da página 1 para a página 2 após salvar a primeira pessoa.

### `trim()`

`trim()` remove espaços vazios antes e depois de uma string.

Exemplo:

```php
trim('   Ana   ');
```

Resultado:

```php
'Ana'
```

Isso evita que o sistema aceite nomes ou valores com espaços escondidos.

### `filter_var()`

`filter_var()` verifica se um valor está no formato certo.

No projeto, ele é usado para validar:

- inteiros com `FILTER_VALIDATE_INT`
- números decimais com `FILTER_VALIDATE_FLOAT`

Isso é ótimo porque reduz a chance do usuário enviar um valor estranho.

### `htmlspecialchars()`

`htmlspecialchars()` transforma caracteres especiais em texto seguro para HTML.

Exemplo: se alguém escrever `<script>`, isso será exibido como texto e não executado como código.

Isso evita problemas de segurança e também prevents injection no HTML.

---

## Explicação do arquivo `app.php`

Este arquivo guarda a lógica reutilizável do projeto.

### `normalizarTema()`

Essa função garante que o valor do tema seja sempre válido. Ela recebe um texto e aceita somente `claro` ou `escuro`. Se vier outra coisa, volta para `claro`.

### `obterTemaPreferido()`

Lê o tema que está salvo no cookie. Se não existir, usa `claro` como padrão.

### `salvarTemaPreferido()`

Salva a escolha do tema em cookie e também na sessão. Isso faz o site manter a mesma aparência no momento atual e também depois de recarregar a página.

### `limparTexto()`

Limpa o texto enviado pelo usuário removing espaços extras e convertendo para string. Isso ajuda a evitar dados inconsistentes.

### `limparInteiroOuNulo()`

Converte o valor para inteiro quando possível. Se o usuário mandar um valor vazio ou inválido, retorna `null`.

### `limparDecimalOuNulo()`

Faz a mesma ideia para números decimais. Isso é útil para peso e altura, já que podem ter casas decimais.

### `validarDadosPessoa()`

É a função mais importante do sistema. Ela valida:

- nome
- idade
- peso
- altura

Se algo estiver inválido, ela devolve uma lista de erros.

### `montarDadosPessoa()`

Depois da validação, essa função organiza os dados da pessoa em um array limpo e pronto para ser salvo em sessão.

---

## Por que usar esse padrão?

Esse projeto usa uma estrutura simples para mostrar a lógica de aplicação web em PHP:

- HTML para a tela
- PHP para processar o formulário
- sessão para guardar informações temporárias
- cookie para lembrar preferências
- validação para impedir dados ruins

Tudo isso mostra o fluxo real que muitas aplicações web usam no começo.

---

## Como executar o projeto

No terminal, rode:

```bash
php -S 0.0.0.0:8000 -t /workspaces/Yurigmelo
```

Depois abra no navegador:

```text
http://localhost:8000/
```

---

## Resumo final

O projeto foi pensado para ser simples, visualmente limpo e fácil de entender.

Ele ensina de forma prática:

- como receber dados em PHP
- como validar entradas
- como guardar dados em sessão
- como salvar preferências em cookie
- como navegar entre páginas mantendo o contexto
- como mostrar um resumo com dados de duas pessoas

E, por isso, ele serve muito bem como exemplo didático e como base para outros projetos maiores.
