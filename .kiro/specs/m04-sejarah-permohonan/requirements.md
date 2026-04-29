# Requirements Document: M04 Sejarah Permohonan

## Introduction

This document specifies the requirements for UC05 — Lihat Sejarah Permohonan (View Application History) feature within the M04 Kemaskini Portal module. This feature enables authenticated applicants (pemohon) to view their complete application history with search, sorting, pagination, and optional export capabilities while maintaining strict security boundaries.

## Glossary

- **Application_History_System**: The Livewire component and backend logic that displays historical portal update applications
- **Applicant**: A user with the "pemohon" role who submits portal update applications
- **Application**: A PermohonanPortal record representing a request to update portal content
- **Ticket_Number**: A unique identifier for each application in the format #ICT-YYYY-NNN
- **Page_URL**: The URL of the portal page that the applicant wants to update
- **Change_Type**: The type of modification requested (jenis_perubahan field)
- **Application_Date**: The date when the application was submitted (created_at timestamp)
- **Completion_Date**: The date when the application was marked as complete (tarikh_selesai field)
- **Status**: The current state of the application (diterima, dalam_proses, selesai)
- **Search_Query**: User input for filtering applications by ticket number or page URL
- **Debounce**: A delay mechanism that prevents query execution until user stops typing
- **Authenticated_User**: The currently logged-in user whose identity is verified by the auth middleware
- **Query_Scope**: A reusable Eloquent query filter defined on the PermohonanPortal model

## Requirements

### Requirement 1: Display Complete Application History

**User Story:** As an applicant, I want to view all my past applications including completed ones, so that I can track my submission history.

#### Acceptance Criteria

1. THE Application_History_System SHALL display all Application records where pemohon_id matches the Authenticated_User ID
2. THE Application_History_System SHALL include Application records with Status "selesai" (completed) in the display
3. THE Application_History_System SHALL include Application records with Status "diterima" (received) in the display
4. THE Application_History_System SHALL include Application records with Status "dalam_proses" (in progress) in the display
5. FOR ALL displayed Application records, the pemohon_id SHALL equal the Authenticated_User ID (invariant property)

### Requirement 2: Display Application Details

**User Story:** As an applicant, I want to see key details for each application, so that I can quickly identify specific submissions.

#### Acceptance Criteria

1. FOR EACH Application record, THE Application_History_System SHALL display the Ticket_Number
2. FOR EACH Application record, THE Application_History_System SHALL display the Page_URL
3. FOR EACH Application record, THE Application_History_System SHALL display the Change_Type
4. FOR EACH Application record, THE Application_History_System SHALL display the Application_Date in "d M Y" format
5. FOR EACH Application record, THE Application_History_System SHALL display the Completion_Date in "d M Y" format
6. WHEN Completion_Date is null, THE Application_History_System SHALL display "—" instead of the date
7. FOR EACH Application record, THE Application_History_System SHALL display the Status with appropriate visual styling

### Requirement 3: Real-Time Search Functionality

**User Story:** As an applicant, I want to search my applications by ticket number or URL, so that I can quickly find specific submissions.

#### Acceptance Criteria

1. THE Application_History_System SHALL provide a search input field for entering Search_Query
2. WHEN Search_Query is not empty, THE Application_History_System SHALL filter Application records where Ticket_Number contains the Search_Query
3. WHEN Search_Query is not empty, THE Application_History_System SHALL filter Application records where Page_URL contains the Search_Query
4. WHEN Search_Query matches both Ticket_Number and Page_URL criteria, THE Application_History_System SHALL display all matching records (OR logic)
5. WHEN Search_Query is empty, THE Application_History_System SHALL display all Application records belonging to the Authenticated_User
6. THE Application_History_System SHALL apply Debounce of 400 milliseconds to Search_Query input
7. WHEN the user types in the search field, THE Application_History_System SHALL wait 400 milliseconds after the last keystroke before executing the query
8. THE Application_History_System SHALL update the displayed results without full page reload (reactive behavior)

### Requirement 4: Sort by Latest Date

**User Story:** As an applicant, I want to see my most recent applications first, so that I can quickly access current submissions.

#### Acceptance Criteria

1. THE Application_History_System SHALL sort Application records by Application_Date in descending order (latest first)
2. FOR ALL displayed Application records, Application_Date of record N SHALL be greater than or equal to Application_Date of record N+1 (sorted order invariant)

### Requirement 5: Pagination

**User Story:** As an applicant, I want applications displayed in manageable pages, so that the interface remains responsive with large datasets.

#### Acceptance Criteria

1. THE Application_History_System SHALL display 15 Application records per page
2. WHEN the total number of Application records exceeds 15, THE Application_History_System SHALL provide pagination controls
3. WHEN the user clicks a pagination control, THE Application_History_System SHALL navigate to the requested page
4. WHEN Search_Query changes, THE Application_History_System SHALL reset pagination to page 1
5. THE Application_History_System SHALL preserve Search_Query when navigating between pages

### Requirement 6: Security - Applicant Data Isolation

**User Story:** As an applicant, I want to ensure that I can only view my own applications, so that my data remains private and secure.

#### Acceptance Criteria

1. THE Application_History_System SHALL filter all queries by pemohon_id equal to Authenticated_User ID
2. IF a user attempts to access the Application_History_System without authentication, THEN THE Application_History_System SHALL redirect to the login page
3. IF a user with a role other than "pemohon" attempts to access the Application_History_System, THEN THE Application_History_System SHALL deny access
4. THE Application_History_System SHALL NOT display Application records where pemohon_id does not match the Authenticated_User ID
5. FOR ALL Application records displayed, pemohon_id SHALL equal Authenticated_User ID (security invariant)
6. THE Application_History_System SHALL apply the pemohon_id filter at the database query level, not in the presentation layer

### Requirement 7: Query Performance Protection

**User Story:** As a system administrator, I want search queries to be debounced, so that the database is not flooded with excessive queries.

#### Acceptance Criteria

1. THE Application_History_System SHALL NOT execute a search query until 400 milliseconds after the user stops typing
2. WHEN the user types continuously, THE Application_History_System SHALL cancel pending queries and wait for the Debounce period
3. THE Application_History_System SHALL execute at most one query per 400 millisecond Debounce period during continuous typing

### Requirement 8: Navigation to Application Details

**User Story:** As an applicant, I want to click on a ticket number to view full application details, so that I can review specific submissions.

#### Acceptance Criteria

1. FOR EACH displayed Ticket_Number, THE Application_History_System SHALL render it as a clickable link
2. WHEN the user clicks a Ticket_Number link, THE Application_History_System SHALL navigate to the application detail page for that Application
3. THE Application_History_System SHALL construct the detail page URL using the route name "m04.show" with the Application ID as parameter

### Requirement 9: Empty State Handling

**User Story:** As an applicant, I want to see a clear message when no applications match my search, so that I understand the system is working correctly.

#### Acceptance Criteria

1. WHEN no Application records match the current Search_Query and filters, THE Application_History_System SHALL display the message "Tiada rekod ditemui."
2. WHEN the Authenticated_User has no Application records, THE Application_History_System SHALL display the message "Tiada rekod ditemui."

### Requirement 10: Optional Export Functionality

**User Story:** As an applicant, I want to export my application history to PDF or Excel, so that I can maintain offline records.

#### Acceptance Criteria

1. WHERE export functionality is enabled, THE Application_History_System SHALL provide a PDF export option
2. WHERE export functionality is enabled, THE Application_History_System SHALL provide an Excel export option
3. WHERE export functionality is enabled, WHEN the user requests a PDF export, THE Application_History_System SHALL generate a PDF containing all Application records matching the current Search_Query and filters
4. WHERE export functionality is enabled, WHEN the user requests an Excel export, THE Application_History_System SHALL generate an Excel file containing all Application records matching the current Search_Query and filters
5. WHERE export functionality is enabled, THE Application_History_System SHALL include the same fields in exports as displayed in the table view

### Requirement 11: Query Scope Implementation

**User Story:** As a developer, I want reusable query scopes on the PermohonanPortal model, so that security filters are consistently applied.

#### Acceptance Criteria

1. THE PermohonanPortal model SHALL provide a Query_Scope named "milikPemohon" that filters by pemohon_id equal to Authenticated_User ID
2. THE PermohonanPortal model SHALL provide a Query_Scope named "carian" that accepts a Search_Query parameter
3. WHEN the "carian" Query_Scope is applied with a Search_Query, THE PermohonanPortal model SHALL filter records where Ticket_Number contains the Search_Query OR Page_URL contains the Search_Query
4. THE Application_History_System SHALL use the "milikPemohon" Query_Scope for all database queries
5. WHEN Search_Query is not empty, THE Application_History_System SHALL use the "carian" Query_Scope

---

*ICTServe M04 | UC05 — Lihat Sejarah Permohonan | Requirements Document*
