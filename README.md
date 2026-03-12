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
