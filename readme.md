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

    - Set `APP_KEY` to random 32 characters long string
    
    - If you set `SQL_LOG_QUERIES` or `SQL_LOG_SLOW_QUERIES` to true, make sure you have created directory set as `SQL_LOG_DIRECTORY` in storage path and you have valid permissions to create and override files in this directory    

3. Run    

    ```
    php artisan jwt:secret
    ```
    
    to generate random `JWT_SECRET` token in your `.env` file
    
### Issues

IDE helper generator seems not to work at the moment in Lumen:

```php 
   artisan ide-helper:generate
   Segmentation fault
```

```php 
artisan ide-helper:models
Exception: Missing argument 1 for Illuminate\Database\Eloquent\Model::morphedByMany(), called in /home/vagrant/Code/cep_backend/vendor/barryvdh/laravel-ide-helper/src/Console/ModelsCommand.php on line 365 and defined
   Could not analyze class App\User.
```
