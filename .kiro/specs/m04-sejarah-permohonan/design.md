# Design Document: M04 Sejarah Permohonan

## Overview

The M04 Sejarah Permohonan (Application History) feature enables authenticated applicants to view their complete portal update application history with real-time search, sorting, and pagination capabilities. This feature is implemented as a Livewire component following the existing M04 module patterns, with strict security boundaries enforced at the database query level.

### Key Design Principles

1. **Security-First Architecture**: All data access is filtered by `pemohon_id` at the query level, not in the presentation layer
2. **Performance Optimization**: Search queries are debounced to prevent database flooding
3. **Consistency**: Follows existing M04 module patterns (SenaraiPermohonan, BorangPermohonan)
4. **Reactive UI**: Livewire provides real-time updates without full page reloads
5. **Reusability**: Query scopes on the model enable consistent security filters across the application

### Scope

**In Scope:**
- Display all application records (diterima, dalam_proses, selesai) for authenticated applicant
- Real-time search by ticket number and page URL with 400ms debounce
- Sorting by application date (latest first)
- Pagination (15 records per page)
- Navigation to application detail page
- Empty state handling
- Query scopes on PermohonanPortal model

**Out of Scope:**
- Export functionality (PDF/Excel) — marked as optional in requirements, deferred to future iteration
- Filtering by status — not in requirements
- Bulk operations
- Admin view of all applications

---

## Architecture

### Component Structure

```
┌─────────────────────────────────────────────────────────┐
│                    Browser (User)                        │
└────────────────────┬────────────────────────────────────┘
                     │
                     │ HTTP Request (GET /kemaskini-portal/sejarah)
                     │ Middleware: auth, role:pemohon
                     ▼
┌─────────────────────────────────────────────────────────┐
│              Route (web.php)                             │
│  Route::get('/kemaskini-portal/sejarah', ...)           │
└────────────────────┬────────────────────────────────────┘
                     │
                     │ Renders Livewire Component
                     ▼
┌─────────────────────────────────────────────────────────┐
│     Livewire Component: SejarahPermohonan               │
│  - Properties: $carian (search query)                   │
│  - Methods: render(), updatedCarian()                   │
│  - Traits: WithPagination                               │
└────────────────────┬────────────────────────────────────┘
                     │
                     │ Query with Scopes
                     ▼
┌─────────────────────────────────────────────────────────┐
│         Model: PermohonanPortal                         │
│  - Scope: milikPemohon(Auth::id())                      │
│  - Scope: carian($query)                                │
│  - Relationships: pemohon, pentadbir, lampirans         │
└────────────────────┬────────────────────────────────────┘
                     │
                     │ SQL Query (filtered by pemohon_id)
                     ▼
┌─────────────────────────────────────────────────────────┐
│              Database (permohonan_portals)              │
└─────────────────────────────────────────────────────────┘
```

### Data Flow

1. **User Request**: User navigates to `/kemaskini-portal/sejarah`
2. **Authentication**: `auth` middleware verifies user is logged in
3. **Authorization**: `role:pemohon` middleware verifies user has pemohon role
4. **Component Initialization**: Livewire SejarahPermohonan component is rendered
5. **Query Execution**: Component queries PermohonanPortal with `milikPemohon` scope
6. **Search Input**: User types in search field
7. **Debounce**: Livewire waits 400ms after last keystroke
8. **Query Update**: Component re-queries with `carian` scope applied
9. **View Rendering**: Blade template displays filtered, paginated results
10. **Navigation**: User clicks ticket number to view details

### Security Architecture

```
┌─────────────────────────────────────────────────────────┐
│                  Security Layers                         │
├─────────────────────────────────────────────────────────┤
│  Layer 1: Route Middleware (auth)                       │
│  - Redirects unauthenticated users to login             │
├─────────────────────────────────────────────────────────┤
│  Layer 2: Role Middleware (role:pemohon)                │
│  - Returns 403 for non-pemohon users                    │
├─────────────────────────────────────────────────────────┤
│  Layer 3: Query Scope (milikPemohon)                    │
│  - Filters WHERE pemohon_id = Auth::id()                │
│  - Applied at database level, not presentation          │
├─────────────────────────────────────────────────────────┤
│  Layer 4: Model Relationship                            │
│  - pemohon() BelongsTo relationship validates ownership │
└─────────────────────────────────────────────────────────┘
```

---

## Components and Interfaces

### 1. Livewire Component: SejarahPermohonan

**Location**: `app/Livewire/M04/SejarahPermohonan.php`

**Responsibilities**:
- Manage search query state
- Execute database queries with security filters
- Handle pagination
- Render view with filtered data

**Properties**:
```php
public string $carian = '';  // Search query (URL-synced)
```

**Methods**:
```php
public function updatedCarian(): void
// Resets pagination to page 1 when search query changes

public function render(): View
// Executes query and returns view with paginated results
```

**Traits**:
- `WithPagination`: Provides pagination functionality

**Attributes**:
- `#[Title('Sejarah Permohonan')]`: Sets page title

### 2. Model Scopes: PermohonanPortal

**Location**: `app/Models/PermohonanPortal.php`

**New Scopes**:

```php
public function scopeMilikPemohon(Builder $query): Builder
// Filters records by authenticated user's ID
// Usage: PermohonanPortal::milikPemohon()->get()

public function scopeCarian(Builder $query, string $carian): Builder
// Filters records by ticket number OR page URL
// Usage: PermohonanPortal::carian($searchTerm)->get()
```

**Existing Relationships** (used by this feature):
- `pemohon()`: BelongsTo User
- `pentadbir()`: BelongsTo User
- `lampirans()`: HasMany Lampiran
- `logAudits()`: HasMany LogAuditPortal

**Existing Casts** (used by this feature):
- `status`: StatusPermohonanPortal enum
- `tarikh_mohon`: datetime (alias for created_at)
- `tarikh_selesai`: datetime (nullable)

### 3. Route Definition

**Location**: `routes/web.php`

**New Route**:
```php
Route::middleware(['auth', 'verified', 'profile.complete'])
    ->prefix('kemaskini-portal')
    ->name('kemaskini-portal.')
    ->group(function () {
        // Existing routes...
        Route::get('/sejarah', \App\Livewire\M04\SejarahPermohonan::class)
            ->name('sejarah')
            ->middleware('role:pemohon');
    });
```

**Route Parameters**:
- **Path**: `/kemaskini-portal/sejarah`
- **Name**: `kemaskini-portal.sejarah`
- **Middleware**: `auth`, `verified`, `profile.complete`, `role:pemohon`
- **Component**: `App\Livewire\M04\SejarahPermohonan`

### 4. Blade View

**Location**: `resources/views/livewire/m04/sejarah-permohonan.blade.php`

**Structure**:
```blade
<div>
    {{-- Search Input --}}
    <flux:input 
        wire:model.live.debounce.400ms="carian" 
        placeholder="Cari no. tiket atau URL..."
    />

    {{-- Data Table --}}
    <flux:table :paginate="$senarai">
        <flux:table.columns>
            <flux:table.column>No. Tiket</flux:table.column>
            <flux:table.column>URL Halaman</flux:table.column>
            <flux:table.column>Jenis</flux:table.column>
            <flux:table.column>Tarikh Mohon</flux:table.column>
            <flux:table.column>Tarikh Selesai</flux:table.column>
            <flux:table.column>Status</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($senarai as $item)
                {{-- Table row with data --}}
            @empty
                {{-- Empty state: "Tiada rekod ditemui." --}}
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
```

**UI Components** (Flux UI):
- `flux:input`: Search input with debounce
- `flux:table`: Data table with pagination
- `flux:badge`: Status and change type badges
- `flux:button`: View details button

---

## Data Models

### PermohonanPortal Model

**Table**: `permohonan_portals`

**Relevant Fields**:
```php
id                  : bigint (primary key)
no_tiket            : string (unique, indexed) - Format: #ICT-YYYY-NNN
pemohon_id          : bigint (foreign key to users) - SECURITY FILTER
pentadbir_id        : bigint (nullable, foreign key to users)
url_halaman         : string - SEARCHABLE
jenis_perubahan     : enum('kandungan', 'konfigurasi', 'lain_lain')
butiran_kemaskini   : text
status              : enum (StatusPermohonanPortal)
tarikh_selesai      : datetime (nullable)
created_at          : datetime (tarikh_mohon) - SORTABLE
updated_at          : datetime
```

**Indexes**:
- Primary key: `id`
- Foreign key: `pemohon_id` (indexed for performance)
- Unique: `no_tiket`
- Composite: `(pemohon_id, created_at)` for optimized queries

**Status Enum Values**:
- `diterima`: Application received
- `dalam_proses`: Application in progress
- `selesai`: Application completed

**Change Type Values**:
- `kandungan`: Content changes
- `konfigurasi`: Configuration changes
- `lain_lain`: Other changes

### Query Patterns

**Base Query** (all history):
```php
PermohonanPortal::milikPemohon()
    ->latest()
    ->paginate(15);
```

**Search Query**:
```php
PermohonanPortal::milikPemohon()
    ->when($this->carian, fn($q) => $q->carian($this->carian))
    ->latest()
    ->paginate(15);
```

**Generated SQL** (with search):
```sql
SELECT * FROM permohonan_portals
WHERE pemohon_id = ?
  AND (no_tiket LIKE ? OR url_halaman LIKE ?)
ORDER BY created_at DESC
LIMIT 15 OFFSET 0;
```

---

## Error Handling

### Authentication Errors

**Scenario**: Unauthenticated user attempts to access the page

**Handling**:
- Middleware: `auth`
- Action: Redirect to login page
- Route: `route('login')`
- Message: Session flash message "Please log in to continue"

### Authorization Errors

**Scenario**: Authenticated user without pemohon role attempts to access the page

**Handling**:
- Middleware: `role:pemohon`
- Action: Return HTTP 403 Forbidden
- View: `errors.403` (Laravel default)
- Message: "You do not have permission to access this page"

### Empty State

**Scenario**: No applications match search criteria or user has no applications

**Handling**:
- Check: `$senarai->isEmpty()`
- Display: "Tiada rekod ditemui." in table body
- Styling: Centered text with muted color
- No error thrown: This is a valid state

### Database Errors

**Scenario**: Database connection failure or query error

**Handling**:
- Laravel Exception Handler catches `QueryException`
- Log: Error logged to `storage/logs/laravel.log`
- User Message: "An error occurred. Please try again later."
- Retry: User can refresh page to retry

### Invalid Search Input

**Scenario**: User enters special characters or very long search query

**Handling**:
- Sanitization: Laravel query builder automatically escapes SQL
- Length: No explicit limit (database field limits apply)
- Special chars: Treated as literal search characters
- No validation errors: All input is valid for search

---

## Testing Strategy

### Why Property-Based Testing Does NOT Apply

This feature is **not suitable for property-based testing** because:

1. **Simple CRUD Operations**: The feature primarily performs database reads with filters — no complex data transformations or business logic algorithms
2. **UI Rendering**: The main output is HTML table rendering, which is better tested with snapshot or integration tests
3. **External Dependencies**: The feature depends on database state, authentication context, and HTTP requests
4. **No Universal Properties**: There are no meaningful "for all inputs X, property P(X) holds" statements to test
5. **Deterministic Behavior**: The search and filter logic is straightforward — 100 iterations won't find more bugs than 2-3 well-chosen examples

**Appropriate Testing Approaches**:
- **Unit Tests**: Test query scopes in isolation
- **Feature Tests**: Test complete user flows with HTTP requests
- **Integration Tests**: Test security boundaries and data isolation

### Unit Tests

**Test File**: `tests/Unit/Models/PermohonanPortalTest.php`

**Test Cases**:

1. **Scope: milikPemohon**
   ```php
   test('milikPemohon scope filters by authenticated user id')
   ```
   - Setup: Create applications for multiple users
   - Action: Query with `milikPemohon()` scope
   - Assert: Only applications belonging to authenticated user are returned

2. **Scope: carian (ticket number)**
   ```php
   test('carian scope filters by ticket number')
   ```
   - Setup: Create applications with different ticket numbers
   - Action: Query with `carian('#ICT-2024-001')`
   - Assert: Only applications with matching ticket number are returned

3. **Scope: carian (page URL)**
   ```php
   test('carian scope filters by page URL')
   ```
   - Setup: Create applications with different URLs
   - Action: Query with `carian('example.com')`
   - Assert: Only applications with matching URL are returned

4. **Scope: carian (OR logic)**
   ```php
   test('carian scope uses OR logic for ticket and URL')
   ```
   - Setup: Create application with ticket #ICT-2024-001 and URL example.com
   - Action: Query with `carian('ICT')` and `carian('example')`
   - Assert: Application is returned for both searches

5. **Scope: carian (partial match)**
   ```php
   test('carian scope performs partial match')
   ```
   - Setup: Create application with ticket #ICT-2024-001
   - Action: Query with `carian('2024')`
   - Assert: Application is returned

6. **Scope: carian (case insensitive)**
   ```php
   test('carian scope is case insensitive')
   ```
   - Setup: Create application with URL Example.com
   - Action: Query with `carian('example')`
   - Assert: Application is returned

### Feature Tests

**Test File**: `tests/Feature/M04/SejarahPermohonanTest.php`

**Test Cases**:

1. **Authentication Required**
   ```php
   test('unauthenticated users are redirected to login')
   ```
   - Action: GET `/kemaskini-portal/sejarah` without authentication
   - Assert: Redirect to login page (302)

2. **Role Authorization**
   ```php
   test('non-pemohon users receive 403 forbidden')
   ```
   - Setup: Authenticate as pentadbir user
   - Action: GET `/kemaskini-portal/sejarah`
   - Assert: HTTP 403 response

3. **Display Own Applications**
   ```php
   test('pemohon sees only their own applications')
   ```
   - Setup: Create applications for user A and user B
   - Action: Authenticate as user A, GET `/kemaskini-portal/sejarah`
   - Assert: Only user A's applications are displayed

4. **Display All Statuses**
   ```php
   test('all application statuses are displayed')
   ```
   - Setup: Create applications with diterima, dalam_proses, selesai statuses
   - Action: GET `/kemaskini-portal/sejarah`
   - Assert: All three applications are displayed

5. **Search by Ticket Number**
   ```php
   test('search filters by ticket number')
   ```
   - Setup: Create applications with different ticket numbers
   - Action: GET `/kemaskini-portal/sejarah?carian=ICT-2024-001`
   - Assert: Only matching application is displayed

6. **Search by Page URL**
   ```php
   test('search filters by page URL')
   ```
   - Setup: Create applications with different URLs
   - Action: GET `/kemaskini-portal/sejarah?carian=example.com`
   - Assert: Only matching application is displayed

7. **Empty State**
   ```php
   test('empty state is displayed when no applications exist')
   ```
   - Setup: No applications for authenticated user
   - Action: GET `/kemaskini-portal/sejarah`
   - Assert: "Tiada rekod ditemui." message is displayed

8. **Empty Search Results**
   ```php
   test('empty state is displayed when search has no results')
   ```
   - Setup: Create applications
   - Action: GET `/kemaskini-portal/sejarah?carian=nonexistent`
   - Assert: "Tiada rekod ditemui." message is displayed

9. **Pagination**
   ```php
   test('applications are paginated at 15 per page')
   ```
   - Setup: Create 20 applications
   - Action: GET `/kemaskini-portal/sejarah`
   - Assert: 15 applications on page 1, 5 on page 2

10. **Pagination Preserves Search**
    ```php
    test('search query is preserved across pagination')
    ```
    - Setup: Create 20 applications matching search
    - Action: GET `/kemaskini-portal/sejarah?carian=test&page=2`
    - Assert: Search query is still applied on page 2

11. **Sort by Latest**
    ```php
    test('applications are sorted by latest first')
    ```
    - Setup: Create applications with different dates
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Applications are ordered by created_at DESC

12. **Ticket Number Link**
    ```php
    test('ticket number links to detail page')
    ```
    - Setup: Create application
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Ticket number is a link to `route('kemaskini-portal.show', $application)`

13. **Debounce Behavior** (Livewire test)
    ```php
    test('search input is debounced')
    ```
    - Action: Type in search field rapidly
    - Assert: Query is not executed until 400ms after last keystroke

### Integration Tests

**Test File**: `tests/Integration/M04/SejarahPermohonanSecurityTest.php`

**Test Cases**:

1. **Data Isolation**
   ```php
   test('users cannot access other users applications via query manipulation')
   ```
   - Setup: Create application for user A
   - Action: Authenticate as user B, attempt to access via direct query
   - Assert: No data is returned

2. **SQL Injection Protection**
   ```php
   test('search input is protected against SQL injection')
   ```
   - Setup: Create applications
   - Action: Search with SQL injection attempt `'; DROP TABLE users; --`
   - Assert: No error, search treats input as literal string

3. **XSS Protection**
   ```php
   test('search results are escaped to prevent XSS')
   ```
   - Setup: Create application with URL containing `<script>alert('xss')</script>`
   - Action: Display application in table
   - Assert: Script tags are escaped in HTML output

### Test Coverage Goals

- **Unit Tests**: 100% coverage of query scopes
- **Feature Tests**: 100% coverage of user-facing functionality
- **Integration Tests**: 100% coverage of security boundaries
- **Overall**: Minimum 90% code coverage for this feature

### Testing Tools

- **Framework**: Pest PHP (existing project standard)
- **Database**: SQLite in-memory for fast tests
- **Factories**: Use existing `PermohonanPortal` factory
- **Assertions**: Pest expectations and Laravel HTTP assertions
- **Livewire Testing**: Livewire test helpers for component tests

---

## Implementation Notes

### Livewire Component Implementation

**Key Considerations**:

1. **URL Syncing**: Use `#[Url]` attribute on `$carian` property to sync search query with URL
   - Enables bookmarking and sharing of search results
   - Preserves search state on page refresh

2. **Debounce**: Use `wire:model.live.debounce.400ms` on search input
   - Prevents excessive database queries
   - Improves user experience (no lag while typing)

3. **Pagination Reset**: Call `$this->resetPage()` in `updatedCarian()` method
   - Ensures user sees page 1 when search changes
   - Prevents confusion from empty pages

4. **Computed Property**: Consider using `#[Computed]` attribute for query results
   - Caches results within request lifecycle
   - Prevents duplicate queries

### Query Scope Implementation

**Best Practices**:

1. **Scope Naming**: Use descriptive names (`milikPemohon`, not `byUser`)
   - Follows Malay naming convention of existing codebase
   - Clear intent for future developers

2. **Scope Composition**: Scopes should be chainable
   ```php
   PermohonanPortal::milikPemohon()->carian($query)->latest()->paginate(15);
   ```

3. **Security**: `milikPemohon` scope should ALWAYS be applied first
   - Prevents accidental data leaks
   - Makes security filter explicit

4. **Performance**: Add database index on `(pemohon_id, created_at)`
   - Optimizes the most common query pattern
   - Improves pagination performance

### View Implementation

**Flux UI Patterns**:

1. **Table Component**: Use `flux:table` with `:paginate` attribute
   - Automatically renders pagination controls
   - Consistent with existing M04 views

2. **Badge Component**: Use `flux:badge` for status display
   - Color coding: `$permohonan->status->color()`
   - Label: `$permohonan->status->label()`

3. **Input Component**: Use `flux:input` with `clearable` attribute
   - Provides clear button for search field
   - Improves user experience

4. **Empty State**: Use `@forelse` directive
   - Cleaner than `@if($senarai->isEmpty())`
   - Consistent with Laravel conventions

### Route Implementation

**Middleware Order**:

1. `auth`: Must be first (establishes user identity)
2. `verified`: Ensures email is verified
3. `profile.complete`: Ensures profile is complete
4. `role:pemohon`: Last (checks authorization)

**Route Grouping**: Place within existing `kemaskini-portal` prefix group
- Maintains consistent URL structure
- Shares middleware configuration

### Migration Considerations

**Database Index**:
```php
$table->index(['pemohon_id', 'created_at']);
```
- Add in new migration if not already present
- Improves query performance for this feature

**No Schema Changes Required**:
- All necessary fields already exist in `permohonan_portals` table
- No new columns needed

---

## Performance Considerations

### Query Optimization

1. **Eager Loading**: Not required for this feature
   - Only displaying fields from `permohonan_portals` table
   - No relationships accessed in table view

2. **Index Usage**: Ensure composite index exists
   - Index: `(pemohon_id, created_at)`
   - Covers WHERE clause and ORDER BY clause
   - Enables index-only scan

3. **Pagination**: Use Laravel's default pagination
   - Efficient LIMIT/OFFSET queries
   - Cached count queries

4. **Search Performance**: LIKE queries are acceptable
   - Limited to 2 columns (no_tiket, url_halaman)
   - Both columns are indexed
   - Debounce prevents excessive queries

### Debounce Strategy

**Why 400ms?**
- Balance between responsiveness and performance
- Typical typing speed: 200-300ms between keystrokes
- 400ms ensures query executes after user pauses

**Alternative Approaches** (not implemented):
- Full-text search: Overkill for 2 columns
- Elasticsearch: Not justified for this use case
- Client-side filtering: Doesn't scale beyond 100 records

### Caching Strategy

**Not Implemented** (by design):
- Application data changes frequently (status updates)
- Search results are user-specific (cannot share cache)
- Pagination makes caching complex
- Database queries are already fast with proper indexes

**Future Consideration**:
- Cache count queries if pagination becomes slow
- Cache status enum labels (already done via enum class)

---

## Security Implementation

### Defense in Depth

**Layer 1: Route Middleware**
```php
Route::middleware(['auth', 'verified', 'profile.complete', 'role:pemohon'])
```
- Prevents unauthenticated access
- Prevents access by non-pemohon users
- Returns 403 for unauthorized users

**Layer 2: Query Scope**
```php
PermohonanPortal::milikPemohon()->...
```
- Filters at database level
- Cannot be bypassed by URL manipulation
- Applied before any other query logic

**Layer 3: Model Relationship**
```php
$permohonan->pemohon()->is(Auth::user())
```
- Additional check in detail view
- Validates ownership before displaying sensitive data

### SQL Injection Prevention

**Laravel Query Builder**:
- Automatically uses prepared statements
- Escapes all user input
- No raw SQL in this feature

**Search Input**:
```php
->where('no_tiket', 'like', "%{$this->carian}%")
```
- `$this->carian` is bound as parameter
- Safe from SQL injection

### XSS Prevention

**Blade Templating**:
```blade
{{ $permohonan->no_tiket }}
```
- Double curly braces automatically escape output
- Prevents script injection

**Livewire**:
- Automatically sanitizes wire:model inputs
- Prevents XSS via search field

### CSRF Protection

**Livewire**:
- Automatically includes CSRF token
- Validates on every request
- No additional configuration needed

### Authorization Checks

**Middleware: EnsureRole**
```php
abort_unless($userRole && in_array($userRole, $roles), 403);
```
- Checks user role against allowed roles
- Returns 403 if unauthorized
- Prevents privilege escalation

---

## Future Enhancements

### Export Functionality (Deferred)

**PDF Export**:
- Library: Laravel DomPDF or Snappy
- Format: Table with same columns as view
- Filename: `sejarah-permohonan-{date}.pdf`
- Trigger: Button in view

**Excel Export**:
- Library: Laravel Excel (Maatwebsite)
- Format: Spreadsheet with same columns as view
- Filename: `sejarah-permohonan-{date}.xlsx`
- Trigger: Button in view

**Implementation Notes**:
- Export should respect current search query
- Export should include all pages (not just current page)
- Export should be queued for large datasets

### Advanced Filtering

**Status Filter**:
- Dropdown to filter by status
- Multiple selection support
- Combines with search query

**Date Range Filter**:
- Filter by application date range
- Filter by completion date range
- Date picker component

**Change Type Filter**:
- Filter by jenis_perubahan
- Multiple selection support

### Sorting

**Additional Sort Columns**:
- Sort by ticket number
- Sort by status
- Sort by completion date

**Implementation**:
- Add `$sortBy` and `$sortDirection` properties
- Add `sort()` method to component
- Add clickable column headers

### Bulk Operations

**Select Multiple**:
- Checkbox for each row
- Select all checkbox
- Bulk actions dropdown

**Bulk Actions**:
- Export selected to PDF/Excel
- Print selected applications

---

*ICTServe M04 | UC05 — Lihat Sejarah Permohonan | Design Document*
