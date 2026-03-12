# Biblioteca

Aplicacao web para gestao de livros, autores e editoras, desenvolvida com Laravel + Livewire + Vite.

## Tecnologias

- PHP / Laravel
- Livewire
- MySQL (ou outro SGBD compativel com Laravel)
- Node.js / npm
- Vite + Tailwind CSS

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- Base de dados configurada no ficheiro .env

## Instalacao

1. Clonar o repositorio.
2. Instalar dependencias PHP e JS.
3. Configurar ambiente.
4. Gerar chave da aplicacao.
5. Executar migracoes e seeders.

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

### Build de producao

```bash
npm run build
```

Nota: os ficheiros gerados em public/build nao devem ser versionados no Git (ja estao no .gitignore).

## Testes

```bash
php artisan test
```

## Fluxo para nova maquina

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
