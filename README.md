# Airline Company Ticket Sale System

## Purpose Of Project - What is it for? 
I created this application as a school project at Yildiz Technical University / Mathematical Engineering.

The topic of school project is developing web application with Laravel and deploying via Laravel Vapor. This repo contains project files / vapor config is ignored.

Airline company ticket sale system selected as demo application topic and some features coded.

To show live demo of project, go to https://airline.emincanozcan.com/

The admin panel can be accessible via https://airline.emincanozcan.com/dashboard

To get administrator account to see admin interface and manage the app, mail me at emincanozcann@gmail.com
## What does the project include?
* Administrator can create flights between airports with some informations which are departure and arrival airports, departure and arrival times, ticket price and flight capacity.
* Administrator can list flights with some filters.
* Visitors can search flights via selecting airports, passenger count and departure date.
* Flight listing function includes direct flights and connected (indirect) flights. To find connected flights, the Flight Search service is developed & it can be found App/Services/FlightSearchService.php 
* Stripe is integrated to system to selling tickets to visitors with credit cards.
* When visitors purchase a ticket, they receive an email with ticket details.
## Technologies used in application
* Laravel
* Jetstream
* Inertia Js
* Tailwind CSS
* Stripe
## How to run application locally?
This project contains Docker configuration for development purposes. It can be run via Docker Compose or other solutions like homestead, valet etc.

To run with docker;

1. Clone the project: `git clone https://github.com/emincanozcan/laravel-airline.git`
2. Go to project root directory: `cd "ProjectDirectory"` 
3. Open `database/seeders/DatabaseSeeder.php` file and change user e-mail and password, these are administrator login credentials.
4. Copy .env.dev to .env: `cp ./.env.dev ./.env`
5. Create a Stripe & Mailgun account and fill the values in .env file
6. Run `docker-compose up --build -d`
7. Run `docker-compose exec php bash` to get in php container
8. Run these commands in the php container;
   1. `composer install`
   2. `php artisan key:generate`
   3. `php artisan migrate --seed`

## License
This project is open-sourced software licensed under the MIT license.
