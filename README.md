# Champions League Simulation

This is a web application that simulates a league tournament between 4 football teams. It simulates match results based on team strength, home advantage, and other factors, and provides championship predictions.

## Features

- Automatically creates 4 teams
- Automatic fixture generation
- Realistic match simulations
- Week-by-week tournament simulation
- Championship prediction system
- League standings table


## Technology Stack

- **Backend:** Laravel 11
- **Frontend:** Vue.js 3 + Inertia.js
- **Database:** MySQL / SQLite (for unit tests)
- **Containerization:** Docker
- **CSS Framework:** Bootstrap 5

## Installation Steps

Follow these steps to run the project in your local development environment.

### 1. Clone the Project

```bash
git clone <https://github.com/mehmetemek/champions-league-simulation.git>
cd champions-league-simulation
```

### 2. Docker Setup

```bash
# Build and start containers
docker-compose up -d --build

# Install Composer dependencies
docker-compose exec app composer install

# Create .env file
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# Setup the database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 3. Frontend Setup

** not required (docker-compose runs this automatically, but can be run if needed)
```bash
# Install Node.js dependencies
docker-compose exec node npm install

# Start frontend in development mode
docker-compose exec node npm run dev
```

### 4. Accessing the Application

After installation is complete, you can access the application by opening the following URL in your browser:
```
http://localhost
```

## Project Structure

- **app/Models:** Database models (Team, Fixture, Game, ScoreBoard)
- **app/Services:** Business logic services (FixtureGeneratorService, MatchSimulatorService, etc.)
- **app/Http/Controllers:** Controller classes
- **resources/js/Pages:** Vue.js components
- **tests/:** Unit tests

## Running Tests

```bash
docker-compose exec app php artisan test
```
