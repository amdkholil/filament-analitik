# Software Requirements Specification (SRS) - Filament Analitik

## 1. Introduction

### 1.1 Purpose
The purpose of this document is to define the requirements for **Filament Analitik**, a lightweight and privacy-focused analytics plugin for the Filament admin panel. It aims to provide developers with essential visitor insights without the overhead of heavy third-party tracking services.

### 1.2 Scope
The system will track visitor activities on specified web routes, store the data locally, and present it through custom widgets and resources within the Filament admin panel.

---

## 2. Functional Requirements

### 2.1 Page View Tracking
- **FR-1.1**: The system shall capture the full URL, request path, and HTTP method.
- **FR-1.2**: The system shall capture the visitor's IP address and User Agent string.
- **FR-1.3**: The system shall exclude requests originating from the Filament admin panel itself to avoid skewing data.
- **FR-1.4**: The system shall only track successful `GET` requests (HTTP 200).

### 2.2 Location Services
- **FR-2.1**: The system shall automatically resolve the visitor's IP address to a geographic location (City, State, Country).
- **FR-2.2**: Geographic resolution shall be performed using the `stevebauman/location` package.

### 2.3 Dashboard & Reporting
- **FR-3.1: Stats Overview Card**: The system shall provide cards showing:
    - **Total Views**: Cumulative count of all recorded page views.
    - **Unique Visitors**: Count of distinct IP addresses recorded.
    - **Views Today**: Count of page views recorded within the current calendar day.
- **FR-3.2: Page Views Line Chart**:
    - The system shall provide a line chart visualizing traffic trends.
    - **Timeframe Options**: 24 hours, 7 days, 14 days, 30 days, and 90 days.
    - **Layout**: Full-width (span) layout with a height approximately 1/2 of the screen.
- **FR-3.3: Geographic Distribution**:
    - The system shall provide a **Pie Chart** showing visitors categorized by Country.
- **FR-3.4: Top Pages Table**:
    - The system shall provide a table showing the **Top 10 most visited pages**.
    - The table must respect the same timeframe filter as the line chart (24h, 7d, etc.).
- **FR-3.5: Filament Resource**:
    - The system shall include a resource to allow administrators to browse, filter, and delete analytics logs.

### 2.4 Asynchronous Processing
- **FR-4.1**: All tracking logic and database operations shall be performed asynchronously using Laravel's Queue system (Jobs).
- **FR-4.2**: The system must ensure that tracking failures do not affect the visitor's experience (fail-safe).

---

## 3. Non-Functional Requirements

### 3.1 Performance
- **NFR-3.1**: Tracking operations must not add more than 10ms to the total request lifecycle (achieved via queueing).
- **NFR-3.2**: Database queries for widgets must be optimized using appropriate indexing on `url` and `path` columns.

### 3.2 Security & Privacy
- **NFR-3.3**: IP addresses shall be stored securely.
- **NFR-3.4**: The system shall support disabling tracking via a configuration flag.

### 3.3 Compatibility
- **NFR-3.5**: The system must support PHP 8.2+.
- **NFR-3.6**: The system must be compatible with Filament v5.0 and Laravel 12.0+.

---

## 4. Technical Architecture

### 4.1 Components
- **Middleware**: `TrackPageView` - Intercepts requests and dispatches jobs.
- **Job**: `TrackPageViewJob` - Performs location lookup and database insertion.
- **Model**: `PageView` - Eloquent model for data persistence.
- **Widgets**: `AnalitikStatsOverview`, `PageViewsChart`, `VisitorsCountryChart`, `TopPagesTable`.
- **Resource**: `PageViewResource`.

### 4.2 Data Schema
The system uses a single table (default: `filament_page_views`) with the following structure:

| Column | Type | Description |
|---|---|---|
| `id` | BigInt | Primary Key |
| `url` | String | Full requested URL (indexed) |
| `path` | String | Request path (indexed) |
| `method` | String | HTTP Method (GET) |
| `ip` | String | Visitor IP Address |
| `user_agent` | Text | Visitor User Agent string |
| `city` | String | Resolved City name |
| `state` | String | Resolved State/Region name |
| `country` | String | Resolved Country name |
| `created_at` | Timestamp | Time of visit |

---

## 5. Appendices
- **Repository**: [kholil/filament-analitik](https://github.com/kholil/filament-analitik)
- **License**: MIT
