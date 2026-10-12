# ProcureHub

Sistema administrativo de compras e contratos com anexos reutilizáveis.

Variação moderna do sistema SISCOMPRA, desenvolvida como projeto full stack para portfólio.

---

## Sobre o projeto

O **ProcureHub** cobre o fluxo principal de um processo de compra:

**Pregão → Itens → Contrato → Ordem de Fornecimento**

Com controle de saldo (financeiro e por item) e um componente de anexos reutilizável em qualquer registro.

Foi construído para demonstrar domínio de stack atual, modelagem de negócio real e código organizado.

---

## Funcionalidades

- Autenticação com Laravel Sanctum (SPA)
- Cadastro de empresas (CNPJ formatado) e produtos (gênero + preço)
- Pregões com itens (quantidade e valor unitário)
- Contratos gerados a partir de um pregão (cópia automática dos itens)
- Ordem de fornecimento que consome saldo do contrato e saldo de quantidade dos itens
- Anexos polimórficos reutilizáveis (empresas, pregões, contratos)
- Páginas de listagem, criação, edição e detalhe
- Dashboard com indicadores (empresas, produtos, pregões, contratos, saldo, ordens do mês)
- Soft deletes com unicidade de número apenas entre registros ativos

---

## Stack

### Backend
- PHP 8.4+
- Laravel 13
- PostgreSQL
- Laravel Sanctum
- Spatie Laravel Permission
- Laravel Sail (Docker — desenvolvimento)

### Frontend
- Vue 3 (Composition API)
- TypeScript
- Pinia
- Vue Router
- Tailwind CSS
- Vite

---

## Requisitos

- Docker + Docker Compose
- Git

---

## Como rodar localmente

### 1. Clone

```bash
git clone https://github.com/LordDrakkarxD/procurehub.git
cd procurehub
```

---

### 2. Suba o ambiente com Sail

```bash
docker run --rm -v $(pwd):/app -w /app composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 3. Acesse

http://localhost

### 4. Scripts úteis

- Subir / parar
```bash
./vendor/bin/sail up -d
./vendor/bin/sail down
```

- Migrations + seeders
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

- Frontend (dev)
```bash
./vendor/bin/sail npm run dev
```

- Build de produção
```bash
./vendor/bin/sail npm run build
```

### 5. Estrutura principal
```bash
app/
  Http/
    Controllers/Api/
    Requests/
  Models/
    Concerns/HasAttachments.php
resources/
  js/
    components/
    pages/
      companies/
      products/
      biddings/
      contracts/
      supply-orders/
    router/
    stores/
    utils/
```

### 6. Fluxo principal

- Cadastrar empresa e produtos
- Criar um pregão e adicionar itens
- Gerar contrato a partir do pregão (itens são copiados)
- Emitir ordem de fornecimento (consome saldo do contrato e quantidade dos itens)
- Acompanhar saldo e indicadores no dashboard

### Autor - Drakar
- Desenvolvido como projeto de portfólio full stack (Laravel + Vue).