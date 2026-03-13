# Travel Orders API

Microsserviço em Laravel para gerenciamento de pedidos de viagem corporativa.

## Tecnologias

- PHP 8.3
- Laravel 11
- MySQL 8.0
- JWT Authentication (tymon/jwt-auth)
- Docker + Docker Compose
- PHPUnit
  
---

## Requisitos

- [Docker](https://www.docker.com/products/docker-desktop/)
- [Git](https://git-scm.com/)

## Instalação e execução

### 1 - Clone o repositório

bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio


### 2 - Suba os containers

bash
docker compose up -d --build


### 3 - Instale as dependências
bash
docker exec travel_app composer install


### 4 - Configure o ambiente
bash
docker exec travel_app cp .env.example .env
docker exec travel_app php artisan key:generate
docker exec travel_app php artisan jwt:secret


### 5 - Configure o banco de dados

No arquivo `.env`, verifique se as variáveis estão assim:
env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=travel_orders
DB_USERNAME=travel_user
DB_PASSWORD=travel123


### 6 - Rode as migrations
bash
docker exec travel_app php artisan migrate


### 7 - Acesse a API

A API estará disponível em: **http://localhost:8000**

--

## Autenticação

A API usa JWT. Para acessar as rotas protegidas, inclua o header:

Authorization: Bearer {token}

---

## Endpoints

### Públicos

 Método: POST
 Rota: /api/register
 Descrição: Registrar usuário
 
 Método: POST 
 Rota: /api/login
 Descrição: Login 

### Protegidos (requer token)

 Método: POST
 Rota: /api/logout
 Descrição: Logout

 Método: GET
 Rota: /api/me
 Descrição: Dados do usuário autenticado

 Método: GET
 Rota: /api/travel-orders
 Descrição: Listar pedidos

 Método: POST
 Rota: /api/travel-orders
 Descrição: Criar pedido

 Método: GET
 Rota: /api/travel-orders/{id}
 Descrição: Consultar pedido

 Método: PATCH
 Rota: /api/travel-orders/{id}/status
 Descrição: Atualizar status

### Filtros disponíveis no GET /api/travel-orders

Parâmetro: status
Exemplo: ?status=approved
Descrição: Filtrar por status

Parâmetro: destination
Exemplo: ?destination=São Paulo 
Descrição: Filtrar por destino

Parâmetro: date_from
Exemplo: ?date_from=2026-01-01
Descrição: Pedidos a partir de

Parâmetro: date_to
Exemplo: ?date_to=2026-12-31
Descrição: Pedidos até

---

## Exemplos de uso

### Registrar usuário
json
POST /api/register
{
    "name": "João Silva",
    "email": "joao@teste.com",
    "password": "123456",
    "password_confirmation": "123456"
}


### Criar pedido de viagem
json
POST /api/travel-orders
{
    "requester_name": "João Silva",
    "destination": "São Paulo",
    "departure_date": "2026-04-01",
    "return_date": "2026-04-10"
}


### Atualizar status
json
PATCH /api/travel-orders/1/status
{
    "status": "approved"
}

---

## Regras de negócio

- Todo pedido é criado com status `requested`
- O dono do pedido **não pode** alterar o próprio status
- Um pedido `cancelled` não pode ser alterado
- Um pedido `approved` pode ser `cancelled`
- Ao aprovar ou cancelar, o dono recebe uma notificação

---

##  Collection Postman

Importe o arquivo `API Traver Order.postman_collection.json` no Postman para testar todos os endpoints já configurados.

### Variáveis de ambiente sugeridas

Variável: base_url
Valor: http://localhost:8000

Variável: token
Valor: preencher após login

---

## Executar os testes
bash
docker exec travel_app php artisan test

---

## Estrutura do projeto

app/
├── Events/
│   └── TravelOrderStatusChanged.php
├── Http/
│   └── Controllers/
│       ├── AuthController.php
│       └── TravelOrderController.php
├── Listeners/
│   └── SendTravelOrderNotification.php
├── Models/
│   ├── TravelOrder.php
│   └── User.php
├── Notifications/
│   └── TravelOrderStatusNotification.php
└── Providers/
    └── AppServiceProvider.php
docker/
├── nginx/
│   └── default.conf
└── Dockerfile
routes/
└── api.php
