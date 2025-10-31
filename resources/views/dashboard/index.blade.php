@extends('dashboard.dashboard')

@section('styles')
	<style>
		.highlights .entry-description h3 {
			color: #333;
		}
		.chart-item-bg .chart-label {
			position: relative;
		}
		.changelog li {
			margin-top: 20px;
			margin-top: 10px;
		}
		.changelog li .status-date {
			font-size: 17px;
			color: #555;
		}
		.changelog li i {
			float: left;
			font-size: 31px;
			margin-right: 10px;
			color: #e5e5e5;
			font-size: 13px;
			color: gold;
		}
		.changelog li p {
			font-size: 13px;
			color: #aaa;
			color: #a0a0a0;
		}
	</style>
@stop

@section('content')

	<div class="row">

		<div class="col-sm-12">
			<div class="chart-item-bg">
				<div class="chart-label">

					<div class="h1 text-muted text-bold">Welcome To Involved Talent</div>

                       {{-- Heading --}}
                       <div class="h1 text-warning text-bold">Update {{ config('app.version') }}</div>
                       <span class="text-small text-muted text-upper">Changelog</span>

                       {{-- Changelog --}}
        <ul class="list-unstyled changelog">
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.18 - S3-Based PDF Generation System:</strong> Complete rewrite of PDF generation using S3 storage, CloudFront CDN, and separate Node.js worker service for high-fidelity PDF rendering with Puppeteer.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Implemented static HTML generation with inlined CSS/JS and absolute image paths - reports now stored in S3 with CloudFront CDN distribution for fast global access.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Created separate pdf-worker Docker service using Puppeteer for browser-based PDF generation - provides high-quality PDFs matching exact web rendering.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Added report_data table migration with slug, html_url, and pdf_url fields - comprehensive tracking of generated report assets in database.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Updated UI with streamlined button workflow: "Run Report" for interactive viewing, "Generate Documents" to create HTML/PDF, "Preview HTML/PDF" for viewing, and removed redundant download button.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented IAM-based S3 authentication for secure, keyless file uploads - no long-lived credentials stored in codebase.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.17 - Enhanced 360 Report Styling & Industry Norms:</strong> Improved 360 report system with enhanced typography, visual growth opportunity indicators, industry norm configuration, and professional spacing and alignment.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Added visual growth opportunity indicators with orange triangles for scores below industry norms, positioned prominently past the end of score bars.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented configurable industry norms system using global database settings - norms now stored in database and easily updatable through admin interface.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Enhanced typography and spacing throughout 360 reports - fixed title line heights, improved score positioning, and better feedback section layout with flexbox.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed empty span rendering issues by adding comprehensive conditional rendering for all dynamic content, preventing messy layout elements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.16 - 360 Report System & Legacy Styling:</strong> Complete 360 report system with legacy-compatible styling, proper feedback grouping, and comprehensive report CSS.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Added legacy-compatible 360 report template with exact styling from original system - proper page layout, score charts, and feedback sections.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed feedback grouping to show single header per feedback type instead of multiple headers for each feedback text.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Extracted and included 2000+ lines of report-specific CSS from legacy system for proper styling of page containers, charts, and feedback sections.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed Laravel 5.1 compatibility issues by removing $loop variables and replacing with manual counters.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed array-to-string conversion errors in feedback display by properly handling nested feedback arrays.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.15 - Assessment Display Improvements:</strong> Removed overlaid title from assessment header and disabled default background fallback for cleaner assessment presentation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Removed white text title overlay that was appearing on top of assessment background images for cleaner visual presentation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Background images now only display when explicitly set - removed fallback to default background.jpg when assessment has no background configured.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.14 - Report Creation & Reminder Email Fixes:</strong> Fixed client reports disappearing after creation and improved reminder email deliverability to avoid spam filters.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed report creation bug - now creates ClientReport pivot record when creating new reports so they persist in the client's report list.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Improved reminder email deliverability by removing emojis from subject lines and body, and using professional business language to avoid spam triggers.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Simplified reminder email template to match basic HTML format of assignment notifications with no CSS styling.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.13 - Modern Assessment Editor & Image Path Fixes:</strong> Updated create assessment page to use modern drag-and-drop editor and fixed missing image 404 errors in assignment details view.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Create assessment now uses modern editor with drag-and-drop field reordering, CKEditor for rich text, and improved user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed image paths in assignment details report view - updated logo.png and report-background.jpg to use /assets/images/ instead of /images/.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed preview mode error by properly maintaining currentPage variable for pagination logic in assessment forms.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.12 - Assessment Description Display Fix:</strong> Fixed duplicate description issue where global description was appearing twice on assessments, once on the intro page and again on page 1 of questions.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Removed description duplication from assessment form - description now only appears on the intro/stage page before starting the assessment.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed issue where paginated assessments would show description on page 1 instead of questions, ensuring first page of questions is never replaced by description.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.11 - Reminder System Fixes & Form Improvements:</strong> Fixed reminder email sending and form processing, corrected date format parsing, and enabled CloudWatch logging for staging environment.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed reminder form field processing - corrected field names to match actual form submission (reminder, reminder-end-date, reminder-end-time).</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented proper date/time parsing for reminder fields using Carbon::createFromFormat with 'D, d M Y h:i A' format.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed date format parsing throughout assignment and mailer code - updated from 'd M Y' to 'D, d M Y' to match datepicker output.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Resolved reminder email sending by implementing synchronous email delivery instead of problematic queue-based approach.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed timestamp tracking by updating last_reminder_sent_at only after successful email delivery, not before.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.10 - Timezone Support for Reminders:</strong> Added intelligent timezone handling for reminder notifications - reminders now respect the admin's local timezone when scheduling, ensuring predictable send times.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>JavaScript auto-detects admin's timezone when creating assignments - reminders sent at the scheduled time in admin's timezone converted to UTC for accurate delivery.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.9 - Reminder System & CloudWatch Monitoring:</strong> Complete reminder notification system for assessments, AWS CloudWatch logging infrastructure, and critical bug fixes for assessment loading.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Implemented automated reminder emails with granular scheduling controls - first reminder date/time, flexible frequencies (30 min, hourly, daily, weekly, bi-weekly), and stop date options.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added Laravel scheduler integration with queue jobs for reliable email delivery - reminders run every 30 minutes and respect assignment expiration dates.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Beautiful responsive reminder email template with urgency indicators, countdown timers, and dynamic subject lines based on days remaining.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>AWS CloudWatch integration - centralized logging for scheduler, application, and system logs with 30-day retention and real-time monitoring.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Host-level cron setup for Laravel scheduler following Docker best practices - easier to maintain and debug than in-container cron.</p>
            </li>
            <li>
                <i class="fa-asterisk text-danger"></i>
                <p>Fixed critical bug: assessments hanging on first page due to infinite 404 loop from incorrect image asset paths in header fallbacks.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Reorganized assignment form with clearer sections: Assessment Settings, Emails, Reminders, and Assign To - improved UX with datetime pickers and dynamic field visibility.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Added comprehensive infrastructure documentation: CloudWatch setup guide, scheduler documentation, and automated setup scripts.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.6 - Critical Bugfixes & Data Export:</strong> Fixed critical issues with server-sent events, assignment URLs, email date formatting, and added user bulk upload improvements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-danger"></i>
                <p>Fixed "Download All Data" button throwing SSE output buffering error - added proper buffer initialization and safety checks.</p>
            </li>
            <li>
                <i class="fa-asterisk text-danger"></i>
                <p>Fixed assignment URL generation linking to 0.0.0.0 - added dynamic URL regeneration accessor to use correct APP_URL from environment.</p>
            </li>
            <li>
                <i class="fa-asterisk text-danger"></i>
                <p>Fixed group assignment email failure due to Carbon date format mismatch - corrected format from 'D, d M Y' to 'd M Y'.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Removed "Add To Job" field from user bulk upload form - simplified user import workflow.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Added detailed error logging for EventSource downloads to help diagnose connection issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.5 - Assignment Form Improvements & Editor Updates:</strong> Streamlined assignment workflow by making modern editor the default, removing unnecessary fields, and adding email reminder functionality for better user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Made modern assessment editor the default 'General' tab - legacy editor removed from navigation for cleaner interface.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Removed 'Lock To Specific Job' field and 'From Job Family' button from assignment form - simplified workflow by removing rarely-used features.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Added Email Reminder functionality to assignment form with frequency options (1 Week, 2 Weeks, 3 Weeks, Monthly) for consistent reminder management across both assignment interfaces.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.4 - Modern Assessment Editor Enhancements:</strong> Completed migration to modern assessment editor with missing fields from legacy editor, fixed rich text HTML preservation, and resolved file upload issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added missing assessment fields to modern editor: Logo upload, Background image, Pagination settings, Timed assessment options, and Assessment target selection (Self/Other/Group Leader).</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Fixed rich text content preservation - tables with borders, padding, and styling now display correctly without being stripped.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed logo and background image uploads to properly save to S3/CloudFront instead of storing temporary file paths.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Added conditional field display for Items Per Page (shows when pagination enabled) and Time Limit (shows when timed assessment enabled).</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.3 - Assessment UX Improvements:</strong> Significantly improved user experience for assessment takers with cleaner interface, dedicated instruction page, and removal of confusing numbering elements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Removed question numbering from user-facing assessment views - numbers are still visible in admin edit/preview modes for easy reference.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Removed "QUESTIONS" heading for a cleaner, more professional assessment interface.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Made assessment description a dedicated first page - for paginated assessments, instructions now appear on page 1, with questions starting on page 2. Instructions no longer repeat on every page.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.2 - Email Configuration & Deployment Fixes:</strong> Fixed production email system by implementing IAM role-based SES authentication, preventing deployment scripts from resetting to broken Mailtrap configuration. Updated both staging and production deployment workflows to explicitly configure SES email settings on every deployment.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented IAM role-based authentication for Amazon SES - no credentials needed, more secure, follows AWS best practices.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed deployment scripts that were resetting email configuration to Mailtrap on every deployment, causing all production emails to fail.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added comprehensive email testing scripts for both staging and production environments with detailed error reporting.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p>Added error handling and graceful error messages to assignments page for improved user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.6.0 - Assessment Editor Overhaul & Drag-and-Drop Restoration:</strong> Major release featuring complete drag-and-drop functionality restoration, fixed field positioning, enhanced delete functionality, and modern UI/UX improvements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-danger"></i>
                <p><strong>v1.6.1 - Critical Bug Fix:</strong> Fixed critical "Whoops" error on assignments page - resolved undefined variable error in AssignmentsController@assignmentsForDate method that was causing 500 errors when clicking on assignment date/time links.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed new field positioning issue - fields now correctly appear at the bottom of the list instead of in the second position.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved delete button getting stuck on "Deleting..." after multiple deletions by implementing proper state management and timeout protection.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Completely restored drag-and-drop functionality using jQuery UI Sortable, removing all Vue.js dependencies for better compatibility.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Enhanced field ordering system with complete reordering approach - backend now processes all fields in frontend order for proper sequential numbering.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added rich text support with full HTML table functionality in description fields, including proper CKEditor integration and content sanitization.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Improved field type management by making field types read-only after creation, preventing bugs and ensuring proper UX flow for field type changes.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced delete functionality with modal confirmation, proper error handling, and 10-second timeout protection to prevent infinite "Deleting..." state.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.41 - Assessment Editor UI/UX Improvements & Anchor Management:</strong> Enhanced modern assessment editor with improved user interface, fixed anchor persistence, and streamlined field management.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed anchor value persistence in database by properly handling JSON serialization/deserialization in AssessmentsController update_questions method.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved "Array to string conversion" error in assessment editor by using model accessors for proper anchor data handling in edit and editNew methods.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated field preview to display anchors as UI elements instead of raw Blade code, improving visual consistency and user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed modal positioning and z-index issues to prevent backdrop from covering modal content and ensure proper scrolling behavior.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Improved field numbering styling by changing from toggle-like circular badges to clean rectangular question numbers with subtle gray styling.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Removed unnecessary field type badge that served no functional purpose, reducing visual clutter in the assessment editor interface.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced field header layout with better spacing, alignment, and non-interactive styling to prevent user confusion.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.40 - Rich Text WYSIWYG Editor & Modern Assessment Editor:</strong> Added comprehensive rich text editing capabilities and completely new modern assessment editor with flexible field reordering.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Integrated CKEditor for rich text WYSIWYG editing of field content with full formatting toolbar including bold, italic, lists, links, colors, and HTML source editing.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Created new modern assessment editor interface with drag-and-drop field reordering, comprehensive edit functionality, and real-time preview capabilities.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added new assessment editor routes (editNew, updateNew) and controller methods with support for all field types including multiple choice, description, text input, letters, and equations.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Implemented field management features: add, edit, duplicate, remove fields with dimension assignment, anchor management for multiple choice options, and practice question toggles.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced Docker build process to include gulp compilation for proper asset management and added modern-assessment-editor.js to gulpfile for automated compilation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.39 - Assessment Dimension Consistency Fix:</strong> Fixed dimension selection inconsistency in assessment editor where input fields showed all dimensions instead of assessment-specific ones.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated AssessmentsController.edit() method to load only dimensions belonging to the specific assessment being edited, ensuring consistency between dimensions tab and input field options.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved discrepancy where assessment input fields displayed a more complete list of dimensions than what was shown in the assessment's dimensions tab.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.37 - Reports & Assignment Flow Fixes:</strong> Resolved critical JavaScript errors in assignment forms and reports functionality with enhanced user experience improvements.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed assignment form JavaScript errors by adding proper modal variable definitions to all user selection functions (From Client, From Groups, From Job Family, From Job).</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved "Uncaught ReferenceError: $modal is not defined" errors that were breaking the assignment flow's user selection buttons.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed reports functionality by adding null checks to Report model's getModelAttribute() and getModelFactorsAttribute() methods.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Resolved "Undefined index: model" and "Undefined variable: intro" errors in reports customize functionality.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced language selection to automatically default to English and skip the language selection page for improved user experience.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed _cover.blade.php template by converting @php directive to regular PHP code with proper variable scoping and null checks.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.36 - Major Functionality Restoration & Error Handling:</strong> Comprehensive fixes for CSV uploads, user management, and download functionality with enhanced error handling throughout the system.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed user CSV upload industry field population by updating JavaScript to properly populate industry field after CSV upload.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Replaced Excel library with native CSV generation for user downloads to resolve PHP 7.4+ compatibility issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Added comprehensive error handling for user creation with specific database error messages, industry validation, and email format validation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced frontend error display with structured error messages, success notifications, and graceful AJAX error handling.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated user management system to replace Job Title/Job Family fields with Industry field throughout all interfaces.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed groups CSV upload UI refresh issues and resolved edit functionality errors.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Fixed JavaScript error preventing page refresh after successful groups CSV upload by removing undefined 'opts' variable.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced groups CSV upload success callback with proper console logging and page reload functionality.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed groups edit functionality by removing undefined 'groupUsers' variable from compact() function.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated groups CSV upload to handle both old format (without Group Name) and new format (with Group Name) seamlessly.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.35 - Groups CSV Upload Enhancement:</strong> Converted groups bulk upload from Excel to CSV format with template downloads and enhanced processing capabilities.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added CSV template download for groups with proper headers (Target Name, Target Email, Name, Email, Role) for easy bulk group creation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Converted groups upload from Excel (.xls, .xlsx) to CSV format for better compatibility and easier file management.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Enhanced groups CSV parsing to handle multiple column naming conventions and skip empty rows automatically.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated groups upload UI with modern interface, template download button, and CSV file validation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added comprehensive logging for groups CSV upload debugging and troubleshooting.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.34 - CSV Upload Enhancements & Production Fixes:</strong> Enhanced user bulk upload functionality with template downloads, automatic username generation, and fixed production deployment issues.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Added CSV template download feature with proper headers (Name, Email, Industry, Username) for easy bulk user creation.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Implemented automatic username generation from names when not provided in CSV uploads to prevent validation errors.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed CSV upload blank rows issue by adding validation to skip empty rows and rows without name/email.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Enhanced CSV parsing to support multiple formats including standard (Name, Email, Industry) and Involved-360 formats.</p>
            </li>
            <li>
                <i class="fa-asterisk text-warning"></i>
                <p>Fixed production deployment issues with proper environment file creation and APP_KEY persistence across container restarts.</p>
            </li>
            <li>
                <i class="fa-asterisk text-info"></i>
                <p>Updated production APP_URL to use correct domain (https://my.involvedtalent.com) and added comprehensive debugging for deployments.</p>
            </li>
            <li>
                <i class="fa-asterisk text-success"></i>
                <p><strong>v1.5.30 - Assignment Form Improvements & Test Coverage:</strong> Enhanced assignment forms with better email notifications, removed white label fields, and added comprehensive test coverage.</p>
            </li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed email notification field visibility in client assignment forms - field now properly displays without syntax errors.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed white label field from assignment forms to simplify the user interface and reduce form complexity.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed GroupsController undefined variable error that was causing runtime issues in group management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive test coverage for assignment forms to ensure reliability and prevent regressions.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed test failures related to jobs slug constraint and assessment foreign key issues in the test suite.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Optimized staging deployment workflow for faster and more reliable deployments.</p>
						</li>
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.29 - Assessment Assign Tab Removal:</strong> Removed the assign tab from assessments page to streamline the assignment workflow and reduce confusion.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed assessment assign functionality from assessments page - assignments should now be done through the dedicated assignments section.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Cleaned up routes, controller methods, and templates related to assessment assignment functionality.</p>
						</li>
						<li>
							<i class="fa-asterisk text-success"></i>
							<p><strong>v1.5.28 - Infrastructure Planning & UI Fixes:</strong> Created comprehensive infrastructure plan for dedicated production environment, fixed login page logo, and improved asset management.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added comprehensive infrastructure documentation and deployment planning for dedicated production environment with RDS, ECS, and ALB.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed missing logo.png on login page by updating asset path from images/ to assets/images/ directory.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Added critical Docker container usage rules to .cursorrules for proper command execution.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved asset management by standardizing image paths and running Gulp build to compile all assets.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Enhanced environment variable handling and Docker container usage documentation.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed Involved360AssessmentSeeder question numbering to ensure sequential ordering for assessment input fields.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Resolved GitHub Actions MySQL client installation issues in CI/CD pipelines.</p>
						</li>
						<li>
							<i class="fa-asterisk text-warning"></i>
							<p>Fixed production and staging environment configurations and removed sensitive files from git tracking.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p><strong>v1.5.25 - S3 Configuration & Minor Fixes:</strong> Standardized S3 file uploads across all environments, fixed assessment form 500 errors, and improved assignment URL generation.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Removed conditional S3/local storage logic - all environments now use S3 consistently with CloudFront CDN integration.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed assessment form submission 500 errors and MathJax CDN warning issues for improved form reliability.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Fixed assignment URL generation to use proper domain across all environments for consistent link behavior.</p>
						</li>
						<li>
							<i class="fa-asterisk text-info"></i>
							<p>Improved password reset page logo placement and centered forgot password link for better UX.</p>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
@stop