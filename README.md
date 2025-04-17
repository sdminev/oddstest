# Oddspedia Microservices
## Overview
This repository contains the `reverb-service` from the Oddspedia microservices project, a Laravel WebSocket server powered by Laravel Reverb.

The overall system includes the following services:
- `feed-fetcher-service`: High-frequency fetcher of external feeds
- `feed-parser-service`: Parses XML/JSON feed data
- `feed-transformer-service`: Normalizes and maps feeds into common schema
- `data-processor-service`: Business logic and persistence
- `reverb-service`: Real-time broadcasting using Laravel Reverb
- `mysql`: Centralized database for all services

---

## Service Architecture (Tree Diagram)
```
oddstest/
├── feed-fetcher-service/
├── feed-parser-service/
├── feed-transformer-service/
├── data-processor-service/
├── reverb-service/
│   ├── .env
│   ├── config/
│   └── ...
├── docker-compose.yml
└── mysql (Docker container)
```

---

## Why the Reverb Service Was Broken and How It Was Fixed

### Problem
The `reverb-service` consistently failed to boot or respond due to:
1. **Improper DB connection settings** – `127.0.0.1` from inside the container does not point to the host DB.
2. **No `.env` cleanup** – The environment file had conflicting ports and references.
3. **Startup logic errors** – Trying to call `reverb:serve` which does not exist.

### Solution
- Used `DB_HOST=mysql` in `.env`, matching the service name in `docker-compose.yml`.
- Corrected `REVERB_PORT=6001` and mapped it correctly to the internal port.
- Used the correct artisan command: `php artisan reverb:start`
- Cached config manually to avoid runtime resolution issues.
- Created the MySQL database manually and ran migrations.

---

## Getting Started

### Prerequisites
- Docker
- Docker Compose

### Start the Project
```bash
docker-compose up -d --build
```

### Rebuild the Reverb Service
```bash
docker-compose up -d --build reverb
```

### Run Migrations (if not auto-applied)
```bash
docker exec -it oddstest-reverb-1 php artisan migrate
```

---

## Notes
- Port `6001` is exposed for Reverb.
- All other services use port `8000` internally but are mapped to `8010` - `8013` externally.
- DB username: `root`, password: `root`, DB name: `reverb_db`

---

## Author
Stefan Minev

---

> This repo is part of the Oddspedia Laravel Microservices showcase project.

