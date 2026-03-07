# Feuerwehr Traisa — Website

Website for **Feuerwehr Traisa** (volunteer fire department), built as a headless WordPress + Vue.js SPA.

## Architecture

| Part | Technology | Location |
|------|-----------|----------|
| Backend / CMS | WordPress (headless) | `wpTheme/` |
| Frontend | Vue 2 SPA | `frontend/` |

**Backend** (`wpTheme/`) is deployed to `wordpress.feuerwehr-traisa.de`. It exposes the standard WordPress REST API and custom endpoints under `/wp-json/types/v1/`.

**Frontend** (`frontend/`) is a Vue 2 SPA using Vuetify, Vuex, and Vue Router. It fetches all content from the WordPress API.

## Getting Started

All commands run from the `frontend/` directory:

```bash
yarn install    # Install dependencies
yarn serve      # Dev server (https://fft.local:8080)
yarn build      # Production build
yarn lint       # Lint
yarn lint:fix   # Lint and auto-fix
```

> The dev server requires the local hostname `fft.local` with HTTPS. Add it to `/etc/hosts`:
> ```
> 127.0.0.1  fft.local
> ```

## Frontend Structure

```
frontend/src/
├── api/          # Axios HTTP client
├── router/       # Vue Router routes
├── store/        # Vuex modules (index, sideBar, page, termine, alarms, archive)
├── views/        # Page-level components
├── components/   # Shared components
├── utils/        # Static data (e.g. duty roster JSON)
└── settings.js   # API base URL configuration
```

## WordPress API Endpoints

Custom endpoints registered in `wpTheme/functions.php`:

| Endpoint | Description |
|----------|-------------|
| `GET /types/v1/getIndexInfo/` | Homepage data |
| `GET /types/v1/getSidebarInfo/` | Latest alarm + next 3 meetings |
| `GET /types/v1/getAllMeetings/` | All meetings |
| `GET /types/v1/getAllAlarmsFromYear/` | Alarms by year |
| `GET /types/v1/getAlarmPost/` | Single alarm details |
| `GET /types/v1/getPage/` | Page by slug |
| `GET /types/v1/getArchive/` | Archive content |
| `POST /types/v1/login/` | Authentication |

## Roster Data

The duty roster is stored as a static JSON file at `frontend/src/utils/rosters/eAbtRoster.json`. To update it, convert a CSV export:

```bash
node frontend/scripts/csvToJsonConverter.js
```
