# Simple PHP CRUD API with Docker

This project demonstrates a simple CRUD API using PHP and MySQL with Docker and phpMyAdmin.

## Features
- PHP 8.1 with Apache
- MySQL 8
- phpMyAdmin for DB management
- RESTful API endpoints for user management

## Usage

```bash
docker-compose up --build
```

Test the API via Postman:
- `GET     /users`
- `GET     /users/{id}`
- `POST    /users`
- `PUT     /users/{id}`
- `DELETE  /users/{id}`

Access phpMyAdmin at: [http://localhost:8080](http://localhost:8080)

