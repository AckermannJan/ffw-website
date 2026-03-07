# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the website for **Feuerwehr Traisa** (a German volunteer fire department), built as a **headless WordPress + Vue.js SPA**. The `wpTheme/` directory is a WordPress theme acting as a headless CMS that exposes REST API endpoints consumed by the Vue frontend in `frontend/`.

## Commands

All commands run from the `frontend/` directory:

```bash
npm install         # Install dependencies
npm run serve       # Dev server (https://fft.local:8080)
npm run build       # Production build
npm run lint        # Lint (runs on pre-commit via lint-staged)
npm run lint:fix    # Lint and auto-fix
```

The dev server requires the local hostname `fft.local` with HTTPS — configure `/etc/hosts` accordingly.

## Architecture

### Two-Part System

**Backend (`wpTheme/`)** — WordPress theme deployed to `wordpress.feuerwehr-traisa.de`:
- Exposes the standard WP REST API at `/wp-json/wp/v2/`
- Exposes custom endpoints at `/wp-json/types/v1/` registered in `wpTheme/functions.php`
- Redirects all URL requests to `index.php` via rewrite rules (so Vue Router controls routing)
- Uses the **Toolset/Types plugin** for custom fields (`types_get_field_meta_value()`)
- `wpTheme/index.php` simply renders `<div id="app">` — Vue mounts there

**Frontend (`frontend/src/`)** — Vue 2 SPA:
- API base URLs configured in `frontend/src/settings.js`
- All HTTP calls go through `frontend/src/api/index.js` (Axios)
- Vuex store split into modules: `sideBar`, `index`, `page`, `termine`, `alarms`, `archive`
- Vuetify 2 as the UI component framework
- vue-nprogress for loading indicators, vue-meta for `<head>` management

### Custom WordPress API Endpoints (`wpTheme/functions.php`)

| Endpoint | Purpose |
|---|---|
| `GET /types/v1/getIndexInfo/` | Homepage posts/pages |
| `GET /types/v1/getSidebarInfo/` | Latest alarm + next 3 meetings |
| `GET /types/v1/getAllMeetings/` | All meetings with dates |
| `GET /types/v1/getAllAlarmsFromYear/` | Alarms filtered by year |
| `GET /types/v1/getAlarmPost/` | Single alarm with custom field metadata |
| `GET /types/v1/getPage/` | Page by slug with images |
| `GET /types/v1/getArchive/` | Archive posts/pages |
| `POST /types/v1/login/` | WordPress authentication |

### Frontend Routing (`frontend/src/router/index.js`)

All routes lazy-load their views. Key routes:
- `/` → Home
- `/seite/:pageSlug` → Generic CMS pages
- `/einsatzabteilung` → Active fire department section (with sub-routes for roster and alarms)
- `/termine` → Events
- `/archiv` → Archive
- `/technik` → Equipment
- `/kindergruppen` → Youth groups

### State Initialization

On app mount (`frontend/src/main.js`), two Vuex actions are dispatched immediately:
- `index/getIndexInfo` — loads homepage data
- `sideBar/getSidebarInfo` — loads sidebar content (latest alarm + upcoming meetings)

### Roster Data

The duty roster (`frontend/src/utils/rosters/eAbtRoster.json`) is a static JSON file. The `frontend/scripts/csvToJsonConverter.js` script converts `scripts/dienstplan.csv` to update this file.
