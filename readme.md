## CEP

### Prerequisites

In order to run the application you need to setup server with:

- PHP 7+
- MySQL 5.7+

For development process, you should use [Laravel Homestead](https://laravel.com/docs/5.2/homestead)

### Coding standards

In order to follow code standards, you should use PSR-2, install [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and before each commit you should run this fixer to make sure your code is properly formatted. Base CS fixer configuration file has created as `.php_cs` to make sure every developer uses the same rules for code formatting.   


### Installation

1. Copy `.env.sample` as `.env`

2. In `.env` file:

    - Set `APP_KEY` to random 32 characters long string using the following command:
    
    ```
    php artisan key:generate
    ```
    
    - If you set `SQL_LOG_QUERIES` or `SQL_LOG_SLOW_QUERIES` to true (to log SQL queries), make sure you have created directory set as `SQL_LOG_DIRECTORY` in storage path and you have valid permissions to create and override files in this directory    

3. Run    

    ```
    php artisan jwt:generate
    ```
    
    to generate random `JWT_SECRET` token in your `.env` file

