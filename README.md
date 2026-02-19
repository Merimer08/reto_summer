# 🚀 Reto Summer — PHP + SQL + Deploy Cloud

Proyecto full-stack básico desarrollado en **PHP puro**, evolucionado paso a paso desde una versión local con ficheros JSON hasta una arquitectura cloud moderna usando:

* PHP + Apache (Docker)
* PostgreSQL (Supabase)
* Deploy automático (Render)
* Migraciones SQL versionadas

---

## 📌 Objetivo del proyecto

Aplicación sencilla con:

* Login de usuarios
* Roles (`super_admin`, `admin`, `user`)
* Gestión de usuarios
* Registro de peso
* Objetivos personales

El objetivo REAL del proyecto fue aprender el flujo profesional completo:

```
Local → Git → Docker → Deploy → DB Cloud → SQL migrations
```

---

# 🧱 FASE 1 — Proyecto local (JSON)

Inicialmente el proyecto funcionaba:

* PHP puro
* almacenamiento en archivos JSON
* sesiones PHP

Estructura inicial:

```
assets/
data/
index.php
login.php
users.php
config.php
```

## Problema detectado

* Los datos se perdían fácilmente.
* No escalable.
* Sin base de datos real.

---

# 🎨 FASE 2 — Migración visual a Bootstrap

Se refactorizó la UI:

* Bootstrap 5
* diseño oscuro
* formularios mejorados
* login responsive

---

# 🔐 FASE 3 — Roles y autenticación

Se implementó:

* `super_admin`
* `admin`
* `user`

Funciones clave:

```php
currentUser()
requireLogin()
canManageUsers()
```

---

# 🐳 FASE 4 — Dockerización (nivel PRO)

Se creó un Dockerfile optimizado:

* PHP + Apache
* extensiones necesarias
* soporte PostgreSQL
* permisos para datos

## Dockerfile (idea básica)

```dockerfile
FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_pgsql

COPY . /var/www/html/
```

---

# ☁️ FASE 5 — Deploy en Render

Se desplegó como:

```
Web Service → Docker runtime
```

## Problema encontrado

Render FREE:

* ❌ no permite discos persistentes
* ❌ borra datos al redeploy

Solución:

➡️ mover la base de datos fuera.

---

# 🧠 FASE 6 — Migración a Supabase (PostgreSQL)

Se creó un proyecto en Supabase.

Configuración `.env`:

```env
DB_HOST=xxxxx.pooler.supabase.com
DB_PORT=6543
DB_NAME=postgres
DB_USER=postgres
DB_PASS=******
```

Importante:

Usar **Connection Pooling**, no direct connection.

---

# 🧬 FASE 7 — Migraciones SQL (Senior workflow)

Se creó la carpeta:

```
sql/
```

## Estructura

```
sql/
 ├── schema.sql
 └── seed.sql
```

### schema.sql

Define las tablas:

* users
* weights
* settings

### seed.sql

Inserta usuarios demo:

* super_admin
* admin demo
* user demo

---

# ⚙️ FASE 8 — Sistema de migraciones

Script ejecutable:

```
migrate.php
```

Ejecuta automáticamente:

```bash
php migrate.php
```

Resultado:

```
Running migrations...
Migrations completed
```

---

# 🔥 Arquitectura final

```
User
 │
 ▼
Render (PHP + Docker)
 │
 ▼
Supabase PostgreSQL
```

---

# 📂 Estructura final del proyecto

```
m-tabla/
│
├── assets/
│   ├── css/
│   └── js/
│
├── data/              # (legacy JSON)
│
├── sql/
│   ├── schema.sql
│   └── seed.sql
│
├── config.php
├── migrate.php
├── index.php
├── login.php
├── users.php
├── Dockerfile
├── .env
└── README.md
```

---

# 🧪 Ejecutar localmente

## 1️⃣ Instalar dependencias PHP

Asegurar extensión:

```
pdo_pgsql
```

---

## 2️⃣ Configurar `.env`

```env
DB_HOST=
DB_PORT=
DB_NAME=
DB_USER=
DB_PASS=
```

---

## 3️⃣ Ejecutar migraciones

```bash
php migrate.php
```

---

## 4️⃣ Lanzar proyecto

```bash
http://localhost/m-tabla
```

---

# 🌍 Deploy automático

Cada push a:

```
main
```

➡️ Render hace deploy automático.

---

# ⭐ Aprendizajes clave

✔ pasar de JSON a SQL
✔ uso de Docker en PHP
✔ deploy cloud real
✔ separar app y base de datos
✔ migraciones versionadas
✔ flujo tipo startup

---

# 🚀 Próximos upgrades (Senior)

* CSRF protection
* password reset
* API REST
* Vue frontend
* RBAC avanzado
* Logs y monitoring

---

## 👩‍💻 Autor

Proyecto creado como práctica de evolución real hacia arquitectura profesional.
npm run build
npm run deploy
![alt text](image-1.png)

GJy36XOmaXHSUhy7
https://bzwfcjuzmcnywpzwzadl.supabase.co
sb_publishable_z0H3IDKkpGXUlWqrPuCyFg_n8MUWaMg
postgresql://postgres:[YOUR-PASSWORD]@db.bzwfcjuzmcnywpzwzadl.supabase.co:5432/postgres
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASS
2️⃣ Insertar tu super admin (IMPORTANTE)

Genera el hash desde local:

php -r "echo password_hash('TU_PASSWORD', PASSWORD_DEFAULT);"


Te dará algo así:

$2y$10$......


Luego en Supabase ejecuta:

insert into users (name,email,password,role)
values (
  'Maria',
  'admin@local',
  '$2y$10$PEGA_AQUI_EL_HASH',
  'super_admin'
);

🧠 3️⃣ Qué cambia ahora en tu proyecto

ANTES:

users.json
weights.json
settings.json


AHORA:

PDO + PostgreSQL (Supabase)


Tus funciones pasan de:

readJson()
writeJson()


a:

SELECT / INSERT / UPDATE
edenciales demo
SUPER ADMIN
email: admin@local
pass: admin123

ADMIN
email: admin@test.com
pass: admin123

USER
email: user@test.com
pass: user123