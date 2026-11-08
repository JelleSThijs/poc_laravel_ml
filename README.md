# FruitMatch Laravel POC

This project is a Laravel and Livewire proof of concept for discovering fruit
preferences. Users can browse fruit, swipe through recommendations, manage
their preferences, and add fruit to the catalog. Feedback can be sent to the
separate CleanRL recommendation service to improve future recommendations.

The application uses Laravel Fortify for authentication, SQLite for local
development, Livewire for interactive pages, and Vite with Tailwind CSS for
frontend assets.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- Python 3.10 or newer and `uv` when running the optional CleanRL service

## Setup

From the project root, run:

```sh
composer run setup
```

This installs PHP and JavaScript dependencies, creates `.env` from
`.env.example` when needed, generates the application key, runs the database
migrations, and builds the frontend assets.

Start the local application with:

```sh
composer run dev
```

This starts the Laravel development server, queue worker, and Vite development
server. Open <http://localhost:8000> in a browser.

The default configuration uses SQLite. The local database is stored at
`database/database.sqlite`, so no separate database server is required.

### Manual setup

Run the individual setup steps when you need more control:

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

On Windows PowerShell, use this instead of `cp`:

```powershell
Copy-Item .env.example .env
```

Do not overwrite an existing `.env` file. Review its values before running
migrations or starting the application.

## CleanRL recommendation service

The Laravel application can use the optional local CleanRL service for
recommendations and preference scoring. Configure its URL in `.env`:

```dotenv
CLEANRL_SERVICE_URL=http://127.0.0.1:8001
```

The service is located in the `ml-service` directory. Start it from that
directory with:

```sh
uv sync --python 3.10
uv run fruitmatch-ml serve
```

The Laravel client communicates with the service through:

- `POST /recommend` for the next fruit recommendation.
- `POST /preferences` for user preference scores.
- `POST /feedback` for like and pass feedback.

If the service is unavailable, the Laravel application logs the failed request
and uses its fallback recommendation behavior. The service is optional for
running the core Laravel POC.

## Main application areas

- `app/Livewire/`: interactive fruit, swipe, and preference components.
- `app/Services/`: recommendation and CleanRL client services.
- `app/Models/`: Eloquent models such as `User` and `Food`.
- `resources/views/`: application pages and Livewire views.
- `routes/web.php`: web routes for the home, fruit creation, and preferences
  pages.
- `database/`: migrations, seeders, factories, and the SQLite database.
- `ml-service/`: the optional Python recommendation service.
- `tests/`: Pest feature and unit tests.

## Useful commands

```sh
# Start the full local development environment
composer run dev

# Run the test suite
php artisan test --compact

# Run formatting
vendor/bin/pint --dirty --format agent

# Run static analysis
composer run types:check

# Build production frontend assets
npm run build

# Inspect registered routes
php artisan route:list
```

## Tests and checks

Run the checks relevant to a change before opening a pull request:

```sh
php artisan test --compact
vendor/bin/pint --dirty --format agent
composer run types:check
```

Behavior changes should include matching Pest coverage, especially for
authentication, Livewire actions, validation, and recommendation fallbacks.

## AI usage

ChatGPT and GitHub Copilot were used as development assistants for code
suggestions, documentation, debugging, and troubleshooting. All generated
suggestions were reviewed, tested, and adapted to the conventions of this
project.

AI-generated code must be reviewed before it is committed. Pay particular
attention to authentication, authorization, validation, database queries,
external-service payloads, and Livewire state changes. Do not share `.env`
files, passwords, API keys, access tokens, personal data, or private production
logs with an AI tool.
