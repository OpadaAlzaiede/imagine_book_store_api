
# Laravel Book Api


## Introduction
This is a simple Laravel api. it automates most of the book store basic functionalities. You can browse all books, add them to your cart, and order your cart items.

### Basic functionalities:
1. Auth:
You can register new account, login with existing one and logout from logged account.

2. Book Geners:
**As a user:** you can (view) book genres.

**As an admin:** You can (view, store, update, delete) book genres.


3. Books:
**As a user:** you can view all books.

**As an admin:** You can (view, store, update, delete) books.

4. Cart:
**As a user:** you can view your cart, add book or remove book from it.

5. Orders:
**As a user:** you can view your orders, store new one.

**As an admin:** You can view all orders.


## Installation

1. Clone the repository:
```sh
https://github.com/OpadaAlzaiede/imagine_book_store_api
```

2. Install all dependencies:
```php
composer install
```

3. Copy .env.example file to .env file:
```sh
cp .env.example .env
```

4. Generate the application key:
```
php artisan key:genetrate
```

5. Setup database enviroment variables in .env file:

** You should create a new database with the name provided to the DB_DATABASE varaiable in you DBMS server **

```php
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_username_password
```

6. Run migration files and seed the database:
```
php artisan migrate:fresh --seed
```

** after running the seeder there will be an admin account ready for you with the following credientials**
```
email: admin@test.com
password: admin@admin
```

7. Run the server:
```
php artisan serve
```
## Usage

There is a postman collection attached at docs folder. You can use it in your postman app and discover different endpoints in this api.

Please notice that the api returns a Bearer Token in (register, login) responses, you can use this token in subsequence requests that require the user to be authenticated.
## Running Tests

There are 40+ feature tests to test different scenarios of the api, please feel free to add extra tests.

To run tests, run the following command

```php
  php artisan test
```

