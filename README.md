# **About The Project**
**Pre Order Api App**


## Installation

**1) Download the codebase**

```bash
git clone https://github.com/emrecanbulat/pre-order-api.git
```

**2) Install dependencies**

```bash
composer install
```

**3) Set `.env` values**

*Run following command for generating a `.env` file from `.env.example`*

```bash
cp .env.example .env
```

*You must fill database variables before running the application*

**4) Generate `APP_KEY`**

```bash
php artisan key:generate
```

**5) Create database tables**

```bash
php artisan migrate --seed
```

**6) Run the Project**

```bash
php artisan serve
```


**!! You can find the sample postman collection in the project.**
