# Biblioteca

Aplicação web para gestão de livros, autores e editoras, desenvolvida com Laravel + Livewire + Vite.

## Tecnologias

- PHP / Laravel
- Livewire
- SQLite (configuracao atual)
- Node.js / npm
- Vite + Tailwind CSS

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- SQLite configurado no ficheiro .env

## Funcionalidades

- Gestão de livros, autores e editoras
- Requisições de livros por utilizadores
- Painel de administração para gerir requisições
- Filtros avançados por estado, datas e pesquisa
- Feedback visual de requisições ativas, entregues, etc.
- Sistema de autenticação (admin/cidadão)
- Notificações de ações

## Utilização

- Utilizador cidadão pode requisitar livros e pedir devolução
- Admin pode aceitar, recusar e gerir todas as requisições
- Filtros e pesquisas independentes para cada tabela

## Instalação

1. Clonar o repositório.
2. Instalar dependências PHP e JS.
3. Configurar ambiente.
4. Gerar chave da aplicação.
5. Executar migrações e seeders.

### Comandos (Windows - PowerShell)

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Comandos (Linux/macOS - bash)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Executar o projeto

### Ambiente de desenvolvimento

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

### Build de produção

```bash
npm run build
```

Nota: os ficheiros gerados em public/build não devem ser versionados no Git (já estão no .gitignore).

## Testes

```bash
php artisan test
```

### Testes Pest de Requisições (Feature)

Executar apenas os testes de requisições:

```bash
php artisan test tests/Feature/RequisicaoTest.php
```

Executar cenários individuais (filtro por nome do teste):

```bash
php artisan test --filter "utilizador pode criar requisicao de livro"
php artisan test --filter "requisicao nao pode ser criada sem livro valido"
php artisan test --filter "utilizador pode devolver livro de requisicao ativa"
php artisan test --filter "utilizador ve apenas as suas requisicoes"
php artisan test --filter "nao e possivel requisitar livro sem stock disponivel"
```

### Tabela de Logs (Admin)

- Autenticar como administrador.
- Abrir o menu de logs no painel admin.
- Confirmar que as ações relevantes (ex.: criação/devolução de requisições) ficam registadas.

Sugestão para apresentação: correr os testes no terminal e, de seguida, mostrar a tabela de logs no browser para evidenciar rastreabilidade.

## Atualizar no GitHub

Fluxo recomendado no PowerShell:

```powershell
git status
git add .
git commit -m "feat: testes Pest de requisicoes e logs admin"
git push origin main
```

Se estiveres a trabalhar noutra branch, substitui `main` pelo nome dessa branch.

## Fluxo para nova máquina

Depois de clonar o projeto:

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Autor

Projeto Biblioteca - Gustavo Rodrigues
