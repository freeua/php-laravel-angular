## Mercator Leasing API
This is the brain, the backend of Mercator Leasing Project. It's a 
REST API that will respond to all the petitions from System and Portals.


## Code style
This project follows the PSR-2 Coding Styles to make sure PHP code is readable and well formatted.

[![psr-2-style](https://img.shields.io/badge/code--style-PSR--2-green.svg?style=flat)](https://www.php-fig.org/psr/psr-2/)

To fix psr-2 styles we have to launch this command:

```vendor/bin/phpcbf  --standard=PSR2 src/```

## Tech and frameworks
We used Laravel Framework to build a robust and take advantage of the Active Record implementation with its ORM [Eloquent](https://laravel.com/docs/5.6/eloquent).
As a result of that Framework, we've used [PHP Language](https://php.net), mostly version 7.x.
We used [MySql database](https://www.mysql.com) as it's a standard, robust and used through to many projects. 

Also we recommend to install [docker](https://docs.docker.com/install/) to start environment. 
This also can be done without it, but we really recommend to use it.

## Project Overview
TODO

## Recommended IDE
We strongly recommend [PHPStorm](https://jetbrains.com/phpstorm) as it is the most smart and ready-to-use IDE form php and everything related to web development. It also has everything of WebStorm (Web development IDE), so it'll be  also useful for our [frontend project](https://github.com/JonasRashedi/Mercator/frontend).

## Installation
### PHP & Composer
After installing the last version of PHP disponible for your platform, install our favourite php package manager: [Composer](https://getcomposer.org/download/). To make your life easy, please [the instructions to use it globally over the system](https://getcomposer.org/doc/00-intro.md#globally).

### Install deps
Once we've installed it, we'll run ```composer install``` at the root of the project. We'll wait a bit and 
we'll have all the dependencies updated and ready to use.

## Hosts

Create this hosts in the /etc/hosts file:

```hosts
127.0.0.1 api.mercator.test
127.0.0.1 system.mercator.test
127.0.0.1 portal.mercator.test
```

## Docker (Recommended)

### **Start services**
Set path to docker folder in console and type:

```docker-compose up -d```

This command creates the necessary containers for the app environment.
This is the list of containers created by docker:

- Nginx
- PHP 7.3
- MySQL 5.7 (Persistent databases in docker/data/mysql folder)
- Mailhog

The MySQL connection properties are:

- Host: **127.0.0.1 for running migrations** / **mysql for app use** 
- Port: 3306
- Database: system
- Username: root
- Password: root

The Mailhog properties are:

- Host: mailhog
- Port: 1025

Mailhog ui url:

http://localhost:8025/

### **Stop service**
To kill all the containers type:

```docker-compose kill```

### **Bash access**
- to see which are the running containers: `docker ps`
- to connect to a container as root: `docker exec -it {container_name} bash`

### Vagrant and Homestead
We're going to install [vagrant](https://www.vagrantup.com/intro/getting-started/index.html), as it's our main tool to create our development environment easily and have a API working out of the box. As we have laravel/homestead as our dependency, please review the section _[per project installation](https://laravel.com/docs/5.7/homestead#per-project-installation)_.
If you are using Mac: run ```php vendor/bin/homestead make```
If you are using Windows: run ```vendor\bin\homestead make```
to have our Vagrantfile and Homestead.yaml. We've now configured Vagrant to use Laravel's Homestead settings, and now we'll configure our _Homestead.yaml_ file.

We have an example file `Homestead.yaml.example` that you can copy and rename to Homestead.yaml, overwritting the existing one. As we'll use a db called mercator, our public route is the /public route of our project, we've to keep in mind that. The only thing you should change from Homestead is the folder `~/development-path/Mercator/api` to point the root path of your project. Remember to put the domain finished with .test (we shouldn't use anymore .dev) to our hosts file. We'll put
```hosts
mercator.test    192.168.10.10
```

### Running up our vagrant
Once we've finished updating our Homestead, we'll run our flamant and shiny command ```vagrant up``` that will download and run our virtual machine. Now we can visit http://mercator.test and see that we've running our API (with errors, but don't panic). Now we'll need to run our migrations to have the db structure as it is in our staging environments

### Configuring env and running migrations
When we've finished to configure and run vagrant, we'll make a copy of the .env.example to .env. changing `DB_DATABASE=system` to `DB_DATABASE=homestead` and run the command `php artisan key:generate`. We'll have to connect to our virtual machine through our magic command `vagrant ssh`. Once we're there, write `cd code` folder and run the command to execute migrations `php artisan migrate` and `php artisan db:seed`. To finish and have our beautiful default Laravel instalation page, run `php artisan key:generate`. And now you can visit http://mercator.test and see the page that is telling us we've everything running. If you need to execute `php artisan migrate:fresh` and getting errors from seeders not found you have to run before `composer dump-autoload`.


## Vscode remote debugging

Create this debugging config file in VS Code

```json
{
    // Use IntelliSense to learn about possible attributes.
    // Hover to view descriptions of existing attributes.
    // For more information, visit: https://go.microsoft.com/fwlink/?linkid=830387
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9001,
            "pathMappings":{
                "/var/www/htdocs": "${workspaceRoot}/api"
            }
        },
        {
            "name": "Launch currently open script",
            "type": "php",
            "request": "launch",
            "program": "${file}",
            "cwd": "${fileDirname}",
            "pathMappings": {
                "/var/www/htdocs": "${workspaceRoot}/api"
            },
            "port": 9001
        }
    ]
}
```

## Emails and notifications

Email system in the app should be created in database, use de emails table with this fields:

- key: Unique value to identify the email
- subject: The email subject
- body: The content of the email inside layout, Basic structure would be:

```text
<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td>
<h1>{{ $subject }}</h1>
</td>
</tr>
<tr>
<td>
</td>
</tr>
<tr>
<td align="center">
    {!! $body !!}
    <br /><br />
</td>
</tr>
</table>
```
- vars: Json array with the email variables for using in the future Email editor. Ex: ["{{$user->fullName}}", "{{$url}}"]

The code for calling the email would be:

```code
public function toMail($notifiable)
{
    if (static::$toMailCallback) {
        return call_user_func(static::$toMailCallback, $notifiable);
    }

    list($from, $name) = $this->getFrom();

    return $this->emailService->create(
        $this->emailKey,
        $from,
        $name,
        [
            'subject'   => $this->data['data']['subject'],
            'body'      => $this->data['data']['body'],
            'styles'    => $notifiable->getNotificationStyles(),
            'date'      => Carbon::now()->format('d.m.Y'),
            'time'      => Carbon::now()->format('H:i'),
            'user'      => $notifiable
        ],
        null,
        $this->data['data']['subject']
    );
}
```

Sent emails will create a new notification on database with the below method:

```code
public function toArray($notifiable)
{
    list($from, $name) = $this->getFrom();
    $dbEmail = $this->emailService->get($this->emailKey);

    return [
        'subject'   => $this->data['data']['subject'],
        'from'      => $name,
        'body'      => $this->emailService->getRenderedBody($dbEmail->body, [
            'subject'   => $this->data['data']['subject'],
            'body'      => $this->data['data']['body'],
            'styles'    => $notifiable->getNotificationStyles(),
            'date'      => Carbon::now()->format('d.m.Y'),
            'time'      => Carbon::now()->format('H:i'),
            'user'      => $notifiable
        ])
    ];
}
```