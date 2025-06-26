# Desafio Backend Pleno da Soluct

Este repositório visa disponibilizar o resultado do desafio proposto pela soluct para o cargo de desenvolvedor backend pleno.
Abaixo segue orientações de como configurar e utilizar a API

## Configuração

- Primeiro é necessário clonar o repositório e entrar na pasta

```bash
git clone git@github.com:diego-pealmeida/desafio-backend-pleno-soluct.git \
&& cd desafio-backend-pleno-soluct
```

- Copie o `.env.example` para `.env`

```bash
cp .env.example .env
```

- Instale as dependências do projeto (comando para não necessitar instalar pacotes e extenções locais)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

- Levante todos os containers docker

```bash
docker compose up -d --build
```

- Gere a chave da aplicação

```bash
docker compose exec app php artisan key:generate
```

- Execute as migrations para criação da estrutura do banco

```bash
docker compose exec app php artisan migrate
```

Com isso o sistema já deverá estar pronto para uso!

## Ferramentas de gerenciamento disponíveis

### Nginx

O sistema conta com um nginx para não ser necessário iniciar o laravel manualmente como o comando `serve`, com isso após levantar os containers, estando tudo certo, basta utilizar a API normalmente

### PostgreSQL

Dentre os serviços em containers, há um serviço de banco de dados com PostgreSQL, ou seja, após levantar os containers já deverá ser possível criar as tabelas e manipular os dados corretamente

### PgAdmin4

Acessando a url [http://localhost:5050](http://localhost:5050) você acessa o painel administrativo do PgAdmin para poder visualizar o banco de dados sem necessitar de uma instalação a parte. Você pode usar os dados de `dev@pgadmin.com` e `Mudar@123` como usuário e senha, respectivamente.

### RedisInsight

Acessando a url [http://localhost:5540](http://localhost:5540) você terá acesso ao painel administrativo para o Redis (RedisInsight).

### Supervisor

O sistema possui um supervisor entre seus serviços que ficará responsável por executar as filas, assim não será necessário inicia-las manualmente

### Documentação de API

Foi usada a biblioteca `dedoc/scramble` para geração de uma documentação de API básica e funcional. Para acessar a documentação, após levantar os containers, basta acessar a URL [http://localhost:8000/docs/api](http://localhost:8000/docs/api)

### Sanctum

O sistema conta com authenticação com token para gerenciamento de tarefas, para essa funcionalidade optei por utilizar o Sanctum

### Horizon

Para ajudar na visualização na execução de jobs, foi instalado o Laravel Horizon, e é possível acessar seu painel para URL [http://localhost:8000/horizon](http://localhost:8000/horizon)

### PHPUnit

Para os testes foi utilizado o próprio PHPUnit, e para executa-los é só usar o comando

```bash
docker compose exec app php artisan test
```

## Fluxo de utilização do sistema

Para realizar o gerenciamento de tarefas é necessário estar autenticado na API, e para isso precisamos ter um usuário. Verifique a sessão [Registrar Usuário](http://localhost:8000/docs/api#/operations/user.store) da documentação para criar seu usuário.

Após criar um usuário, é necessário obter um token para autenticação na API, para isso consulte a sessão [Authenticar](http://localhost:8000/docs/api#/operations/auth.login) da documentação, e gere seu token.

Com o token em mão já será possível realizar as operações listas na sessão de `Tarefas` da documentação, é necessário passar o token obtido junto ao header em cada requisição.

Ex.:

```bash
curl --request GET \
  ...
  --header 'Authorization: Bearer {seu_token_aqui}'
```
