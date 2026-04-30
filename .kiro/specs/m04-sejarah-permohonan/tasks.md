# Implementation Plan: M04 Sejarah Permohonan

## Overview

This implementation plan covers the development of the M04 Sejarah Permohonan (Application History) feature, which enables authenticated applicants to view their complete portal update application history with real-time search, sorting, and pagination. The implementation follows existing M04 module patterns and enforces strict security boundaries at the database query level.

## Tasks

- [x] 1. Add database optimization for query performance
  - Add composite index on `permohonan_portals` table for `(pemohon_id, created_at)` columns
  - Create migration file: `database/migrations/YYYY_MM_DD_HHMMSS_add_composite_index_to_permohonan_portals.php`
  - Use `$table->index(['pemohon_id', 'created_at'])` to optimize the most common query pattern
  - _Requirements: 6.6, 7.1_

- [x] 2. Implement query scopes on PermohonanPortal model
  - [x] 2.1 Implement `milikPemohon` scope
    - Add `scopeMilikPemohon(Builder $query): Builder` method to `app/Models/PermohonanPortal.php`
    - Filter records by authenticated user's ID: `$query->where('pemohon_id', Auth::id())`
    - This scope enforces security at the database query level
    - _Requirements: 1.5, 6.1, 6.6, 11.1_
  
  - [x] 2.2 Implement `carian` scope
    - Add `scopeCarian(Builder $query, string $carian): Builder` method to `app/Models/PermohonanPortal.php`
    - Filter records where ticket number OR page URL contains the search query
    - Use `$query->where(function($q) use ($carian) { $q->where('no_tiket', 'like', "%{$carian}%")->orWhere('url_halaman', 'like', "%{$carian}%"); })`
    - _Requirements: 3.2, 3.3, 3.4, 11.2, 11.3_

- [x] 3. Create Livewire component for application history
  - [x] 3.1 Create SejarahPermohonan component
    - Create file: `app/Livewire/M04/SejarahPermohonan.php`
    - Add namespace: `namespace App\Livewire\M04;`
    - Extend `Livewire\Component`
    - Use `WithPagination` trait for pagination functionality
    - Add `#[Title('Sejarah Permohonan')]` attribute for page title
    - _Requirements: 1.1, 1.2, 1.3, 1.4_
  
  - [x] 3.2 Add component properties
    - Add public property: `public string $carian = '';` with `#[Url]` attribute for URL syncing
    - This enables bookmarking and preserves search state on page refresh
    - _Requirements: 3.1, 3.5_
  
  - [x] 3.3 Implement `updatedCarian` method
    - Add method: `public function updatedCarian(): void`
    - Call `$this->resetPage()` to reset pagination to page 1 when search changes
    - This prevents confusion from empty pages after search
    - _Requirements: 4.4_
  
  - [x] 3.4 Implement `render` method with query logic
    - Add method: `public function render(): View`
    - Query `PermohonanPortal::milikPemohon()` to get user's applications
    - Apply `->when($this->carian, fn($q) => $q->carian($this->carian))` for conditional search
    - Sort by `->latest()` (created_at DESC) to show most recent first
    - Paginate with `->paginate(15)` for 15 records per page
    - Return view: `return view('livewire.m04.sejarah-permohonan', ['senarai' => $query]);`
    - _Requirements: 1.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 5.1, 5.2_

- [x] 4. Create Blade view for application history
  - [x] 4.1 Create view file
    - Create file: `resources/views/livewire/m04/sejarah-permohonan.blade.php`
    - Add main container: `<div>`
    - _Requirements: 1.1, 2.1_
  
  - [x] 4.2 Add search input field
    - Use Flux UI component: `<flux:input wire:model.live.debounce.400ms="carian" placeholder="Cari no. tiket atau URL..." clearable />`
    - The `debounce.400ms` prevents excessive database queries while typing
    - The `clearable` attribute provides a clear button for better UX
    - _Requirements: 3.1, 3.6, 3.7, 7.1, 7.2, 7.3_
  
  - [x] 4.3 Create data table structure
    - Use Flux UI table component: `<flux:table :paginate="$senarai">`
    - Add table columns: No. Tiket, URL Halaman, Jenis, Tarikh Mohon, Tarikh Selesai, Status
    - Use `<flux:table.columns>` and `<flux:table.column>` for headers
    - Use `<flux:table.rows>` for data rows
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 5.2_
  
  - [x] 4.4 Implement table data rows
    - Use `@forelse($senarai as $item)` to iterate through applications
    - Display ticket number as clickable link: `<a href="{{ route('kemaskini-portal.show', $item) }}">{{ $item->no_tiket }}</a>`
    - Display page URL: `{{ $item->url_halaman }}`
    - Display change type with badge: `<flux:badge>{{ $item->jenis_perubahan }}</flux:badge>`
    - Display application date: `{{ $item->created_at->format('d M Y') }}`
    - Display completion date: `{{ $item->tarikh_selesai?->format('d M Y') ?? '—' }}`
    - Display status with badge: `<flux:badge :color="$item->status->color()">{{ $item->status->label() }}</flux:badge>`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 8.1, 8.2, 8.3_
  
  - [x] 4.5 Add empty state handling
    - Use `@empty` clause in `@forelse` loop
    - Display message: "Tiada rekod ditemui." when no records match
    - Center the message with appropriate styling
    - _Requirements: 9.1, 9.2_

- [x] 5. Register route with middleware
  - [x] 5.1 Add route to web.php
    - Open file: `routes/web.php`
    - Add route within existing `kemaskini-portal` prefix group
    - Route definition: `Route::get('/sejarah', \App\Livewire\M04\SejarahPermohonan::class)->name('sejarah')->middleware('role:pemohon');`
    - Full path will be: `/kemaskini-portal/sejarah`
    - Full route name will be: `kemaskini-portal.sejarah`
    - _Requirements: 6.2, 6.3, 8.3_
  
  - [x] 5.2 Verify middleware stack
    - Ensure route inherits middleware from group: `auth`, `verified`, `profile.complete`
    - Ensure route has additional middleware: `role:pemohon`
    - This creates defense-in-depth security: authentication → verification → profile → authorization
    - _Requirements: 6.1, 6.2, 6.3_

- [x] 6. Checkpoint - Verify basic functionality
  - Ensure all tests pass
  - Manually test the page loads correctly
  - Verify search functionality works with debounce
  - Verify pagination works correctly
  - Ask the user if questions arise

- [x] 7. Write unit tests for model scopes
  - [x] 7.1 Create unit test file
    - Create file: `tests/Unit/Models/PermohonanPortalTest.php`
    - Add namespace and imports
    - _Requirements: 11.1, 11.2, 11.3_
  
  - [x] 7.2 Test `milikPemohon` scope filters by authenticated user
    - Test name: `test('milikPemohon scope filters by authenticated user id')`
    - Setup: Create applications for multiple users using factory
    - Action: Authenticate as user A, query with `PermohonanPortal::milikPemohon()->get()`
    - Assert: Only applications belonging to user A are returned
    - Assert: Applications belonging to other users are NOT returned
    - _Requirements: 1.5, 6.1, 6.6, 11.1_
  
  - [x] 7.3 Test `carian` scope filters by ticket number
    - Test name: `test('carian scope filters by ticket number')`
    - Setup: Create applications with ticket numbers #ICT-2024-001, #ICT-2024-002, #ICT-2024-003
    - Action: Query with `PermohonanPortal::carian('ICT-2024-001')->get()`
    - Assert: Only application with ticket #ICT-2024-001 is returned
    - _Requirements: 3.2, 11.2, 11.3_
  
  - [x] 7.4 Test `carian` scope filters by page URL
    - Test name: `test('carian scope filters by page URL')`
    - Setup: Create applications with URLs example.com, test.com, demo.com
    - Action: Query with `PermohonanPortal::carian('example')->get()`
    - Assert: Only application with URL containing 'example' is returned
    - _Requirements: 3.3, 11.2, 11.3_
  
  - [x] 7.5 Test `carian` scope uses OR logic
    - Test name: `test('carian scope uses OR logic for ticket and URL')`
    - Setup: Create application with ticket #ICT-2024-001 and URL example.com
    - Action: Query with `PermohonanPortal::carian('ICT')->get()` and `PermohonanPortal::carian('example')->get()`
    - Assert: Application is returned for both searches
    - _Requirements: 3.4, 11.3_
  
  - [x] 7.6 Test `carian` scope performs partial match
    - Test name: `test('carian scope performs partial match')`
    - Setup: Create application with ticket #ICT-2024-001
    - Action: Query with `PermohonanPortal::carian('2024')->get()`
    - Assert: Application is returned (partial match works)
    - _Requirements: 3.2, 3.3_
  
  - [x] 7.7 Test `carian` scope is case insensitive
    - Test name: `test('carian scope is case insensitive')`
    - Setup: Create application with URL Example.com
    - Action: Query with `PermohonanPortal::carian('example')->get()`
    - Assert: Application is returned (case insensitive search)
    - _Requirements: 3.2, 3.3_

- [x] 8. Write feature tests for authentication and authorization
  - [x] 8.1 Create feature test file
    - Create file: `tests/Feature/M04/SejarahPermohonanTest.php`
    - Add namespace and imports
    - _Requirements: 6.1, 6.2, 6.3_
  
  - [x] 8.2 Test unauthenticated users are redirected
    - Test name: `test('unauthenticated users are redirected to login')`
    - Action: GET `/kemaskini-portal/sejarah` without authentication
    - Assert: Response status is 302 (redirect)
    - Assert: Redirect location is login page
    - _Requirements: 6.2_
  
  - [x] 8.3 Test non-pemohon users receive 403
    - Test name: `test('non-pemohon users receive 403 forbidden')`
    - Setup: Create and authenticate as user with role 'pentadbir'
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Response status is 403 (forbidden)
    - _Requirements: 6.3_
  
  - [x] 8.4 Test pemohon sees only their own applications
    - Test name: `test('pemohon sees only their own applications')`
    - Setup: Create user A and user B, create applications for both users
    - Action: Authenticate as user A, GET `/kemaskini-portal/sejarah`
    - Assert: Only user A's applications are displayed in response
    - Assert: User B's applications are NOT displayed
    - _Requirements: 1.5, 6.1, 6.4, 6.5_

- [x] 9. Write feature tests for display and filtering
  - [x] 9.1 Test all application statuses are displayed
    - Test name: `test('all application statuses are displayed')`
    - Setup: Create applications with statuses 'diterima', 'dalam_proses', 'selesai' for authenticated user
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: All three applications are visible in response
    - _Requirements: 1.2, 1.3, 1.4_
  
  - [x] 9.2 Test search filters by ticket number
    - Test name: `test('search filters by ticket number')`
    - Setup: Create applications with different ticket numbers for authenticated user
    - Action: GET `/kemaskini-portal/sejarah?carian=ICT-2024-001`
    - Assert: Only application with matching ticket number is displayed
    - _Requirements: 3.2, 3.8_
  
  - [x] 9.3 Test search filters by page URL
    - Test name: `test('search filters by page URL')`
    - Setup: Create applications with different URLs for authenticated user
    - Action: GET `/kemaskini-portal/sejarah?carian=example.com`
    - Assert: Only application with matching URL is displayed
    - _Requirements: 3.3, 3.8_
  
  - [x] 9.4 Test empty state when no applications exist
    - Test name: `test('empty state is displayed when no applications exist')`
    - Setup: Authenticate user with no applications
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: "Tiada rekod ditemui." message is displayed in response
    - _Requirements: 9.2_
  
  - [x] 9.5 Test empty state when search has no results
    - Test name: `test('empty state is displayed when search has no results')`
    - Setup: Create applications for authenticated user
    - Action: GET `/kemaskini-portal/sejarah?carian=nonexistent`
    - Assert: "Tiada rekod ditemui." message is displayed in response
    - _Requirements: 9.1_

- [x] 10. Write feature tests for pagination and sorting
  - [x] 10.1 Test applications are paginated at 15 per page
    - Test name: `test('applications are paginated at 15 per page')`
    - Setup: Create 20 applications for authenticated user
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Response contains 15 applications
    - Action: GET `/kemaskini-portal/sejarah?page=2`
    - Assert: Response contains 5 applications
    - _Requirements: 5.1, 5.2_
  
  - [x] 10.2 Test search query is preserved across pagination
    - Test name: `test('search query is preserved across pagination')`
    - Setup: Create 20 applications matching search term for authenticated user
    - Action: GET `/kemaskini-portal/sejarah?carian=test&page=2`
    - Assert: Search query is still applied on page 2
    - Assert: Only matching applications are displayed
    - _Requirements: 4.4, 5.5_
  
  - [x] 10.3 Test applications are sorted by latest first
    - Test name: `test('applications are sorted by latest first')`
    - Setup: Create applications with different created_at dates for authenticated user
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Applications are ordered by created_at DESC
    - Assert: First application has the most recent date
    - _Requirements: 4.1, 4.2_
  
  - [x] 10.4 Test ticket number links to detail page
    - Test name: `test('ticket number links to detail page')`
    - Setup: Create application for authenticated user
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Response contains link to `route('kemaskini-portal.show', $application)`
    - Assert: Link text is the ticket number
    - _Requirements: 8.1, 8.2, 8.3_

- [x] 11. Write integration tests for security boundaries
  - [x] 11.1 Create integration test file
    - Create file: `tests/Integration/M04/SejarahPermohonanSecurityTest.php`
    - Add namespace and imports
    - _Requirements: 6.1, 6.4, 6.5, 6.6_
  
  - [x] 11.2 Test users cannot access other users' applications
    - Test name: `test('users cannot access other users applications via query manipulation')`
    - Setup: Create application for user A with known ID
    - Action: Authenticate as user B, attempt to query PermohonanPortal directly with user A's application ID
    - Assert: No data is returned when using `milikPemohon` scope
    - Assert: Security filter is applied at database level
    - _Requirements: 6.4, 6.5, 6.6_
  
  - [x] 11.3 Test search input is protected against SQL injection
    - Test name: `test('search input is protected against SQL injection')`
    - Setup: Create applications for authenticated user
    - Action: GET `/kemaskini-portal/sejarah?carian='; DROP TABLE users; --`
    - Assert: No error occurs (query builder escapes input)
    - Assert: Search treats input as literal string
    - Assert: No database tables are dropped
    - _Requirements: 3.1, 3.2, 3.3_
  
  - [x] 11.4 Test search results are escaped to prevent XSS
    - Test name: `test('search results are escaped to prevent XSS')`
    - Setup: Create application with URL containing `<script>alert('xss')</script>`
    - Action: GET `/kemaskini-portal/sejarah`
    - Assert: Script tags are escaped in HTML output (use `assertSee` with escaped parameter)
    - Assert: JavaScript is not executed
    - _Requirements: 2.2_

- [x] 12. Final checkpoint - Ensure all tests pass
  - Run full test suite: `php artisan test`
  - Verify all unit tests pass
  - Verify all feature tests pass
  - Verify all integration tests pass
  - Manually test the complete user flow
  - Ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Unit tests validate query scopes in isolation
- Feature tests validate complete user flows with HTTP requests
- Integration tests validate security boundaries and data isolation
- The implementation follows existing M04 module patterns (SenaraiPermohonan, BorangPermohonan)
- All security filters are applied at the database query level, not in the presentation layer
- Search queries are debounced at 400ms to prevent database flooding
- Pagination is set to 15 records per page as per requirements

## Implementation Context

This feature is part of the M04 Kemaskini Portal module and enables UC05 — Lihat Sejarah Permohonan (View Application History). The implementation uses:

- **Framework**: Laravel 11 with Livewire 3
- **UI Components**: Flux UI (existing project standard)
- **Testing**: Pest PHP (existing project standard)
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel Fortify
- **Authorization**: Custom role middleware

The feature integrates with existing components:
- `PermohonanPortal` model (app/Models/PermohonanPortal.php)
- `StatusPermohonanPortal` enum (app/Enums/StatusPermohonanPortal.php)
- `EnsureRole` middleware (app/Http/Middleware/EnsureRole.php)
- Existing M04 routes and views

---

*ICTServe M04 | UC05 — Lihat Sejarah Permohonan | Implementation Tasks*
