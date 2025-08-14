# Fluxxer - Gerenciador de Tasks

Este é um projeto Laravel com Vue.js que implementa um sistema de gerenciamento de tasks com filas Redis e Horizon.

## 🚀 Funcionalidades

- **API REST Laravel**: Endpoints para criar e listar tasks
- **Sistema de Filas**: Processamento assíncrono com Redis + Horizon
- **Frontend Vue.js**: Interface moderna para gerenciar tasks
- **Banco PostgreSQL**: Persistência de dados robusta

## 📋 Pré-requisitos

- PHP 8.3+
- Composer
- PostgreSQL
- Redis
- Node.js 18+ (recomendado 20+)
- npm

## 🛠️ Instalação

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd fluxxer
```

### 2. Instale as dependências PHP
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados PostgreSQL
Edite o arquivo `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fluxxer_test
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 5. Crie o banco de dados
```bash
sudo -u postgres createdb fluxxer_test
```

### 6. Execute as migrations
```bash
php artisan migrate
```

### 7. Instale as dependências Node.js
```bash
npm install
```

### 8. Configure o Redis
Certifique-se de que o Redis está rodando:
```bash
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

## 🚀 Executando o Projeto

### 1. Inicie o servidor Laravel
```bash
php artisan serve
```

### 2. Em outro terminal, inicie o Horizon (para as filas)
```bash
php artisan horizon
```

### 3. Em outro terminal, compile os assets
```bash
npm run dev
```

### 4. Acesse a aplicação
Abra http://localhost:8000 no seu navegador

## 📡 Endpoints da API

### POST /api/tasks
Cria uma nova task
```json
{
    "title": "Nome da task"
}
```

### GET /api/tasks
Lista todas as tasks

## 🔄 Sistema de Filas

O projeto utiliza Laravel Horizon para gerenciar as filas Redis:

- **ProcessTask**: Job que simula processamento (sleep 5s) e atualiza status para "done"
- **Monitoramento**: Acesse http://localhost:8000/horizon para monitorar as filas

## 🎨 Frontend

- **Componente Vue**: TaskList.vue com interface moderna
- **Estilização**: CSS customizado com design responsivo
- **Funcionalidades**: Criar tasks, visualizar status, atualização em tempo real

## 🗄️ Estrutura do Banco

### Tabela `tasks`
- `id` (auto-increment)
- `title` (string, obrigatório)
- `status` (enum: pending, processing, done)
- `created_at` / `updated_at` (timestamps)

## 🧪 Testando

### 1. Crie uma task via interface
- Acesse a página principal
- Digite o título da task
- Clique em "Criar Task"

### 2. Monitore o processamento
- A task será criada com status "pending"
- Imediatamente mudará para "processing"
- Após 5 segundos, mudará para "done"

### 3. Verifique as filas
- Acesse http://localhost:8000/horizon
- Monitore o processamento das tasks

## 📁 Estrutura do Projeto

```
fluxxer/
├── app/
│   ├── Http/Controllers/Api/TaskController.php
│   ├── Jobs/ProcessTask.php
│   └── Models/Task.php
├── database/migrations/
├── resources/
│   ├── js/components/TaskList.vue
│   ├── js/app.js
│   └── views/home.blade.php
├── routes/api.php
└── README.md
```

## 🔧 Comandos Úteis

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Verificar status das migrations
php artisan migrate:status

# Reiniciar Horizon
php artisan horizon:terminate
php artisan horizon

# Compilar assets para produção
npm run build
```

## 🐛 Solução de Problemas

### Erro de conexão com PostgreSQL
- Verifique se o PostgreSQL está rodando
- Confirme as credenciais no arquivo `.env`
- Teste a conexão: `php artisan tinker`

### Erro de conexão com Redis
- Verifique se o Redis está rodando: `sudo systemctl status redis-server`
- Teste: `redis-cli ping`

### Problemas com Vue
- Limpe o cache: `npm run dev -- --force`
- Verifique se o plugin Vue está instalado

## 📝 Licença

Este projeto foi criado para fins de teste técnico.

## 👨‍💻 Theo Kalmus