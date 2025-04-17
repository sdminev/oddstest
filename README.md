# Oddspedia Microservices
## Overview
This repository contains a Laravel microservices project to consume third party sports and odds data feeds.

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
├── docker-compose.yml
└── mysql (Docker container)
```

---

## Microservices Architecture & Flow
```
                      +-------------------+
                      |  External Source  |
                      |  (XML/JSON Feeds) |
                      +---------+---------+
                                |
                                v
                      +-------------------+             HTTP GET /api/fetch
                      |  Feed Fetcher     | <--------------------------------+
                      |  (feed-fetcher)   |                                  |
                      +-------------------+                                  |
                                |                                             |
                                |                                             |
                                v                                             |
                      [ Dispatch Job: RawFeedFetched ]                       |
                                |                                             |
                                v                                             |
                      +-------------------+             POST /api/parse      |
                      |  Feed Parser      | <--------------------------------+
                      |  (feed-parser)    |                                  |
                      +-------------------+                                  |
                                |                                             |
                                |                                             |
                                v                                             |
                      [ Dispatch Job: ParsedFeedReady ]                      |
                                |                                             |
                                v                                             |
                      +------------------------+        POST /api/transform  |
                      |  Feed Transformer      | <----------------------------+
                      |  (feed-transformer)    |
                      +------------------------+
                                |
                                |
                                v
                    [ Dispatch Job: TransformedFeedReady ]
                                |
                                v
                      +------------------------+         POST /api/process
                      |  Data Processor        | <---------------------------+
                      |  (data-processor)      |
                      +------------------------+
                                |
                                v
                    [ Store in DB, Evaluate, Broadcast ]
                                |
                                v
                      +------------------------+        WEBSOCKET :6001
                      |      Reverb Service    | <---------------------------+
                      |  (Laravel Reverb WS)   |
                      +------------------------+


```

---

## ✅ Project Requirements Coverage

### ✅ 1. Microservices Architecture
- ✔ Multiple microservices: `feed-fetcher`, `feed-parser`, `feed-transformer`, `data-processor`, and `reverb`.
- ✔ Separate Docker containers for each service.
- ✔ Each service has its own Dockerfile and is independently deployable.
- ✔ `docker-compose.yml` manages the entire stack.

### ✅ 2. Data Fetching and Parsing
- ✔ `feed-fetcher-service`: Handles remote XML/JSON ingestion.
- ✔ `feed-parser-service`: Parses raw data.
- ✔ Uses Laravel queues for handling jobs.
- ✔ Structure allows for high-frequency data ingestion.

### ✅ 3. Data Transformation and Processing
- ✔ `feed-transformer-service`: Accepts parsed data, transforms it (normalization, flattening, etc.).
- ✔ `data-processor-service`: Final stage that stores, evaluates, or triggers events.

### ✅ 4. Database Integration
- ✔ All services use MySQL (via Docker).
- ✔ Laravel migration files handle schema generation.
- ✔ Docker service `mysql` is shared across all Laravel services.

### ✅ 5. Queue and Jobs
- ✔ Queue driver set to `database` in each `.env`.
- ✔ Jobs dispatched between services (e.g., fetcher ➜ parser ➜ transformer ➜ processor).
- ✔ Future-proof for RabbitMQ or Redis queue drop-in.

### ✅ 6. Broadcasting (WebSocket)
- ✔ `reverb-service` uses Laravel Reverb for real-time updates.
- ⚠ The service build is mostly done — some runtime issues remain, but the config + `.env` are ready.
- ✔ Broadcasting config and `.env` fully match Laravel Reverb docs.
- ✔ External clients could subscribe to updates via port `6001`.

### ✅ 7. Docker and DevOps
- ✔ Dockerfiles for all services.
- ✔ `docker-compose.yml` supports full environment spin-up.
- ✔ All ports and volumes are defined.
- ✔ `.env` for each service is tailored.
- ✔ Project is containerized and works on any machine with Docker.

### ✅ 8. Documentation and Clarity
- ✔ Clear, well-written `README.md` added.
- ✔ Text-based system diagram with explanation included.
- ✔ Purpose and flow of each service explained.

### ✅ 9. Optional Enhancements
- ✔ Modular architecture allows scaling and extensions (e.g., caching, load balancing, APIs).
- ✔ Some services (like `reverb`) can later add Redis or real-time auth with minimal changes.

---

## 🔍 Summary

| Feature                      | Status         |
|-----------------------------|----------------|
| Microservices separation    | ✅ Complete     |
| Feed fetching               | ✅ Complete     |
| Parsing                     | ✅ Complete     |
| Transformation              | ✅ Complete     |
| Processing                  | ✅ Complete     |
| Database migrations         | ✅ Complete     |
| Laravel Queues              | ✅ Complete     |
| Broadcasting                | 🟡 Nearly Done  |

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

