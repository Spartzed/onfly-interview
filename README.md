# Sistema de Pedidos de Viagem Corporativa

Uma aplicação Full Stack completa para gerenciamento de pedidos de viagem corporativa, desenvolvida com Laravel (Backend) e Vue.js (Frontend).

## 🚀 Tecnologias Utilizadas

### Backend
- **Laravel 12** - Framework PHP
- **JWT Auth** - Autenticação com tokens JWT
- **MySQL** - Banco de dados
- **Docker** - Containerização

### Frontend
- **Vue.js 3** - Framework JavaScript
- **Vue Router** - Roteamento
- **Pinia** - Gerenciamento de estado
- **Element Plus** - Componentes UI
- **Axios** - Cliente HTTP
- **Vite** - Build tool

## 📋 Funcionalidades

### Backend (Laravel)
- ✅ Criar pedidos de viagem
- ✅ Consultar pedidos por ID
- ✅ Listar todos os pedidos com filtros
- ✅ Atualizar status de pedidos (apenas admin)
- ✅ Cancelar pedidos (apenas solicitados)
- ✅ Notificações automáticas
- ✅ Autenticação JWT
- ✅ Controle de permissões por usuário

### Frontend (Vue.js)
- ✅ Dashboard interativo
- ✅ Formulário de criação de pedidos
- ✅ Atualização de status em tempo real
- ✅ Autenticação com JWT
- ✅ Filtros por status e destino
- ✅ Interface responsiva e moderna
- ✅ Feedback visual para o usuário

## 🏗️ Arquitetura

O projeto segue os princípios de **Domain-Driven Design (DDD)** e **Clean Architecture**:

```
app/
├── Domain/                    # Camada de Domínio
│   ├── TravelOrder/          # Domínio de Pedidos de Viagem
│   │   ├── Entities/         # Entidades
│   │   ├── Enums/           # Enums
│   │   ├── Events/          # Eventos
│   │   ├── Listeners/       # Listeners
│   │   ├── Notifications/   # Notificações
│   │   └── Repositories/    # Interfaces dos Repositories
│   └── User/                # Domínio de Usuários
│       └── Entities/        # Entidades
├── Application/              # Camada de Aplicação
│   └── Services/            # Serviços de Aplicação
├── Infrastructure/           # Camada de Infraestrutura
│   └── Repositories/        # Implementações dos Repositories
└── Http/                    # Camada de Apresentação
    └── Controllers/         # Controllers da API
```

## 🚀 Como Executar

### Pré-requisitos
- Docker e Docker Compose
- Git

### 1. Clone o repositório
```bash
git clone git@github.com:Spartzed/onfly-interview.git
cd onfly-interview
```

### 2. Configure as variáveis de ambiente
```bash
cp .env.example .env
```

Edite o arquivo `.env` com as seguintes configurações:
```env
APP_NAME=Onfly
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_SERVICE=backend

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

JWT_SECRET=

WWWGROUP=1000
WWWUSER=1000
```

### 3. Instalação das Dependências

#### Opção A: Com PHP 8.4 instalado localmente
```bash
composer install
```

#### Opção B: Sem PHP instalado (usando Docker)
```bash
sudo docker run --rm \
  -v $(pwd):/opt \
  -w /opt \
  laravelsail/php84-composer:latest \
  composer install
```

#### Opção extra, definir um alias
```bash
echo "alias sail='bash vendor/bin/sail'" >> ~/.bashrc && source ~/.bashrc
```

### 4. Execute os containers
```bash
sail up -d
```

### 5. Configure a aplicação Laravel
```bash
# Gere a chave da aplicação
sail artisan key:generate

# Execute as migrations
sail artisan jwt:secret

```

### 6. Caso tenha problema de permissão
```bash
sudo chmod 664 .env
```

## 🌐 Acessos

- **Frontend**: http://localhost:3000/login
- **Backend API**: http://localhost/api
- **Mailpit (Emails)**: http://localhost:8025

## 👥 Usuários Padrão

### Admin
- **Email**: admin@example.com
- **Senha**: password

### Usuários Comuns
- **Email**: john@example.com
- **Senha**: password
- **Email**: jane@example.com
- **Senha**: password

## 🧪 Testes

### Executar todos os testes
```bash
sail test
```

### Executar testes específicos
```bash
sail test --filter=TravelOrderServiceTest
```

## 📚 Documentação da API

### Autenticação

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

#### Registro
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "Nome do Usuário",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Pedidos de Viagem

#### Listar Pedidos
```http
GET /api/travel-orders
Authorization: Bearer {token}
```

#### Criar Pedido
```http
POST /api/travel-orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "requester_name": "Nome do Solicitante",
    "destination": "São Paulo, SP",
    "departure_date": "2024-02-01",
    "return_date": "2024-02-05"
}
```

#### Atualizar Status (Admin)
```http
PATCH /api/travel-orders/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "approved"
}
```

#### Cancelar Pedido
```http
DELETE /api/travel-orders/{id}
Authorization: Bearer {token}
```

## 🔧 Desenvolvimento

### Estrutura do Projeto
```
├── app/
│   ├── Domain/              # Domínios da aplicação
│   ├── Application/         # Serviços de aplicação
│   ├── Infrastructure/      # Implementações externas
│   └── Http/               # Controllers e Middleware
├── database/
│   ├── migrations/          # Migrations do banco
│   ├── seeders/            # Seeders
│   └── factories/          # Factories para testes
├── frontend/               # Aplicação Vue.js
│   ├── src/
│   │   ├── components/     # Componentes Vue
│   │   ├── views/          # Páginas
│   │   ├── stores/         # Stores Pinia
│   │   ├── router/         # Configuração de rotas
│   │   └── services/       # Serviços de API
│   └── public/             # Arquivos estáticos
└── tests/                  # Testes automatizados
```

### Comandos Úteis

#### Laravel
```bash
# Executar migrations
sail artisan migrate

# Executar seeders
sail artisan db:seed

# Limpar cache
sail artisan cache:clear

# Gerar chave JWT
sail artisan jwt:secret

# Rodar a fila de email
sail artisan queue:work
```

#### Frontend
```bash
# Instalar dependências
npm install

# Executar em desenvolvimento
npm run dev

# Build para produção
npm run build

# Preview do build
npm run preview
```

## 🎯 Funcionalidades Implementadas

### ✅ Backend
- [x] Arquitetura DDD com Clean Architecture
- [x] Autenticação JWT
- [x] CRUD completo de pedidos de viagem
- [x] Controle de permissões (admin/user)
- [x] Filtros por status, destino e período
- [x] Notificações automáticas
- [x] Validações e regras de negócio
- [x] Testes unitários
- [x] Migrations e Seeders

### ✅ Frontend
- [x] Interface moderna com Element Plus
- [x] Autenticação e proteção de rotas
- [x] Dashboard interativo
- [x] Formulários com validação
- [x] Filtros em tempo real
- [x] Feedback visual para usuário
- [x] Responsividade
- [x] Gerenciamento de estado com Pinia

### ✅ DevOps
- [x] Docker e Docker Compose
- [x] Configuração de ambiente
- [x] Documentação completa
- [x] Estrutura organizada

## 👨‍💻 Autor

Desenvolvido como parte do processo seletivo para a Onfly.

---

**Nota**: Este projeto foi desenvolvido seguindo as melhores práticas de desenvolvimento, incluindo DDD, Clean Architecture, testes automatizados e uma interface moderna e intuitiva.
