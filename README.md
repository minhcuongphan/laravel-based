```
Hi, please follow the instructions below to get started. Don't hesitate to message me if you have any questions about this software and I will try to help you understand it. Thank you!

```

```

## Docker setup

```
#make a copy of env file
cp .env.example .env

##Docker build
docker-compose build

#build docker
docker-compose up -d

#access php container
docker exec -it {app-name}-php bash
```

## Laravel install and build app

```
root@123456789abcde:/var/www/html#composer install
root@123456789abcde:/var/www/html#php artisan key:generate
root@123456789abcde:/var/www/html#composer require weidner/goutte
root@123456789abcde:/var/www/html#composer dump-autoload
```

##Run this command to execute queue jobs

php artisan queue:work --queue=scraping