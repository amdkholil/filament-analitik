# Code Review Findings — Filament Analitik

**Tanggal:** 2026-09-24  
**Scope:** Full codebase review (src, config, database, tests, docs)  
**Test status saat review:** 21 passed (52 assertions)

---

## Ringkasan Eksekutif

Plugin ini solid dan well-scelah (middleware → queued job → model → widgets/resource). Arsitektur bersih, access control thoughtful, driver-aware SQL chart bagus. Temuan terkonsentrasi pada 3 tema:

1. **Repo hygiene** — `tests/TestCase.php` di-gitignore (ship-blocker untuk CI/kontributor)
2. **Fail-safety gap** — job tidak menangkap exception penuh (melanggar SRS FR-4.2)
3. **Test authenticity & docs drift** — beberapa test menyalin query alih-alih memanggil kode widget; beberapa docs tidak sinkron dengan kode

**Keamanan:** Tidak ditemukan vulnerability (XSS/SQLi/auth bypass). Query memakai Eloquent bindings, access control deny-by-default untuk guest, tidak ada static leakage tersisa.

---

## Status Fix Sebelumnya (6 temuan round pertama)

| # | Temuan | Status | Bukti |
|---|--------|--------|-------|
| 1 | `table_name` default tidak konsisten → unifikasi `'analitik'` | ✅ FIXED | config, model, migration, README, AGENTS.md, CLAUDE.md, SRS |
| 2 | `$canAccessCallback` static → instance | ✅ FIXED | `FilamentAnalitikPlugin.php:14` instance property; test anti-leak ada |
| 3 | Sparkline dummy → query harian 7 hari | ✅ FIXED | `AnalitikStatsOverview.php` `dailyViews()`/`dailyUniqueIps()` |
| 4 | phpunit.xml Unit suite (folder tidak ada) | ✅ FIXED | hanya testsuite Feature |
| 5 | Rename migration → `create_analitik_table.php` | ✅ FIXED | file sudah diganti |
| 6 | TestCase panel registration timing | ✅ FIXED (working tree) | `getEnvironmentSetUp` + `Filament::registerPanel` — **tapi ter-block H1** |

---

## Temuan Round 2 (Review Mendalam)

### 🔴 HIGH

#### H1 — `tests/TestCase.php` di-gitignore, tidak pernah di-commit
- **Lokasi:** `.gitignore:9` → `/tests/TestCase.php`
- **Dampak:** Fresh clone / CI tidak bisa run `vendor/bin/pest` sama sekali (`Pest.php` → class not found). Fix #6 hanya ada di mesin ini.
- **Action:** Hapus baris `.gitignore`, commit `tests/TestCase.php`.
- **Status:** ✅ Fixed (baris dihapus)

#### H2 — Job tidak fail-safe; failure bisa break visitor request (langgar SRS FR-4.2)
- **Lokasi:** `src/Jobs/TrackPageViewJob.php`
- **Dampak:** Dengan `QUEUE_CONNECTION=sync`, exception dari `PageView::create()` muncul di middleware → visitor dapat 500. Trigger umum: migration belum publish, URL > 255 char. Tidak ada `failed()`, `$tries`, `backoff`.
- **Action:** Wrap seluruh `handle()` di try/catch `\Throwable` + `report()`; tambah `failed()`; `$tries = 3`.
- **Status:** ✅ Fixed

---

### 🟡 MEDIUM

#### M3 — `url` column `varchar(255)` tapi middleware simpan `fullUrl()` (termasuk query string)
- **Lokasi:** migration `string('url')` vs `TrackPageView.php` `$request->fullUrl()`
- **Dampak:** URL dengan query string panjang → MySQL strict mode "Data too long" → job failure (kompound H2).
- **Action:** Ubah ke `text('url')`.
- **Status:** ✅ Fixed

#### M4 — Tidak ada bot/crawler filtering
- **Lokasi:** `TrackPageView.php` — semua GET 200 di-track
- **Dampak:** Googlebot, uptime monitor, link previewer mem-blow-up metrik. README klaim "analytics" → data quality rusak.
- **Action:** Config `exclude_bots` + `bot_patterns`; skip empty UA.
- **Status:** ✅ Fixed

#### M5 — Multi-tenant `project_id`: write-side only, widgets unscoped, config mutation global
- **Lokasi:** `FilamentAnalitikPlugin::register()` mutates global config; widgets query seluruh table
- **Dampak:** Dashboard campur semua project tanpa scoping; multi-panel → last registration wins.
- **Action:** Scope semua widget query by `config project_id` saat set; dokumentasi batasan multi-panel.
- **Status:** ✅ Fixed (widgets scoped)

#### M6 — `PageViewsChart` 24h filter double-count boundary hour
- **Lokasi:** `PageViewsChart.php` rolling `subDay()` tapi bucket clock-hour `'H:00'`
- **Dampak:** Data kemarin jam parsial merge ke bucket hari ini → overcount.
- **Action:** Bucket by full datetime truncated to hour (`Y-m-d H:00`); align window ke `startOfHour()`.
- **Status:** ✅ Fixed

#### M7 — Widget tests re-implement query alih-alih panggil kode widget (false confidence)
- **Lokasi:** `WidgetTest.php` — chart/top-pages/top-countries tests salin SQL inline
- **Dampak:** Regresi di `getData()`/`table()` tetap pass.
- **Action:** Invoke widget via reflection / extract `getTableQuery()` / `Livewire::test()`.
- **Status:** ✅ Fixed (extract `getTableQuery()`, reflection `getData()`/`getStats()`)

#### M8 — `ViewAction` dengan form schema kosong → modal kosong
- **Lokasi:** `PageViewResource.php` `form()` zero components + `ViewAction::make()`
- **Dampak:** Klik View → modal kosong hanya tombol Close. Tidak ada view page.
- **Action:** Hapus `ViewAction` (data sudah terlihat di kolom table).
- **Status:** ✅ Fixed

#### M9 — `AnalitikStatsOverview` jalankan ~21 COUNT query per render
- **Lokasi:** `AnalitikStatsOverview.php` — `dailyViews()` dipanggil 2×, loop 7 query each
- **Dampak:** ~24 query per page load.
- **Action:** Memoize series; reuse `$views` untuk dua stat.
- **Status:** ✅ Fixed

#### M10 — Privacy: full query string persist; klaim "privacy-friendly" berlebihan
- **Lokasi:** `url` = `fullUrl()` simpan query string verbatim (token, email, signed URL)
- **Dampak:** Product/privacy exposure — bukan vulnerability, tapi perlu keputusan eksplisit.
- **Action:** Pertimbangkan strip/allowlist query string + dokumentasi retention.
- **Status:** ⏸ Open (butuh keputusan product)

---

### 🔵 LOW

| ID | Temuan | Status |
|----|--------|--------|
| L1 | "Views Today" description bilang "last 24 hours" tapi value calendar day | ✅ Fixed → "Views today (calendar day)" |
| L2 | Default filter beda: chart `'1'` vs tables `'7'` | ✅ Fixed → semua default `'7'` |
| L3 | `sortable()` pada kolom aggregate pre-sorted (no-op/confusing) | ✅ Fixed → hapus `sortable()` |
| L4 | Octane staleness `Filament::getCurrentPanel()` | ✅ Fixed → tambah `$request->routeIs('filament.*')` |
| L5 | `path` tanpa leading slash (`test-page` vs `/test`) | ✅ Fixed → normalize `'/' . ltrim(...)` |
| L6 | `catch (\Exception)` miss `\Throwable` | ✅ Fixed → `catch (Throwable)` |
| L7 | Local dummy IP `8.8.8.8` hardcode | ⏸ Documented (acceptable for dev) |
| L8 | Docs drift (CLAUDE Unit suite, release assessment counts, Laravel matrix, SRS missing `project_id`, README widget count, `canAccessUsing` snippet) | ⚠ Partial — lihat bawah |
| L9 | `composer.lock` out of date | ⏸ `composer update` lokal |
| L10 | Code style: no `declare(strict_types=1)`, `getTable()` missing return type, trailing whitespace, unordered use | ⚠ Partial — `getTable(): string` + `$fillable` fixed |
| L11 | Widget filter tidak di-whitelist (non-numeric → empty) | ✅ Fixed → `resolveFilter()` whitelist |
| L12 | `VisitorsCountryChart` empty data → blank pie | ⏸ Cosmetic |

---

## Rekomendasi Lanjutan (belum dikerjakan)

1. **Docs pass penuh (L8):**
   - `CLAUDE.md` — hapus "Feature & Unit" → hanya Feature
   - `doc/release_maturity_assessment.md` — update count test (sekarang >21) & klaim "10/10 bebas bug"
   - `doc/SRS.md` — tambah kolom `project_id` di schema table; update NFR-3.6 compatibility matrix
   - `README.md` — fitur widgets = 5 (bukan 2); caveat queue sync untuk "zero performance"; contoh `canAccessUsing` harus chained inside `plugins([...])`
   - `tests/TestCase.php` — comment stale "via resolving()"
2. **Style sweep (L10):** `declare(strict_types=1)` di semua file src/config/database; Laravel Pint; trailing whitespace; ordered use statements
3. **Test authenticity tambahan:** kasus HEAD/3xx/long-URL/bot/job DB-failure; assert sparkline *values* bukan hanya count
4. **`composer update`** untuk refresh lock
5. **M10 privacy decision:** strip query string config option
6. **Commit** seluruh working tree (belum di-commit semua)

---

## Test Matrix Saat Review

```
Tests:    21 passed (52 assertions)
Duration: ~1s (serial) / ~0.7s (parallel)
```

| Suite | Tests | Status |
|-------|-------|--------|
| AccessTest | 6 | ✅ |
| NavigationTest | 3 | ✅ |
| PageViewResourceTest | 1 | ✅ |
| TrackPageViewTest | 7 | ✅ |
| WidgetTest | 4 | ✅ |
