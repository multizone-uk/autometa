# Changelog

All notable changes to the AutoMeta project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Plugin

### [1.2.3] - 2025-11-10

#### Fixed
- Removed file logging entirely to prevent permission warnings
- Fixed logging permission errors by checking directory writability
- Improved error handling and logging stability

### [1.2.2] - 2025-11-09

#### Fixed
- Fixed plugin display error caused by permission denied warnings
- Enhanced plugin initialization reliability

### [1.2.1] - 2025-11-08

#### Added
- Added conditional debug mode (can be enabled/disabled in plugin settings)
- Created shared MetaDescriptionHelper utility for code consistency
- Improved file-based logging for troubleshooting

#### Fixed
- Fixed manifest filename to match Joomla expectations

### [1.2.0] - 2025-11-01

#### Changed
- Modernized for Joomla 5 compatibility
- Implemented PSR-4 autoloading and namespaces
- Added service provider pattern

### [1.0.0] - 2024

#### Added
- Initial release
- Automatically generates meta descriptions from article title and content
- Configurable max length, separator, and source options
- Smart truncation at word boundaries
- Option to include/exclude title or content
- Option to overwrite existing descriptions

## Component

### [2.1.0] - 2025-11-12

#### Added
- **Analytics Dashboard** (subscription-gated): Track and display regeneration statistics
  - Recent regenerations table showing last 5 regeneration events with user, date, processed count, and success rate
  - Top regenerated articles table showing top 5 most regenerated articles with hit counts
  - Database tables for tracking regeneration history and article statistics
  - Article hit count tracking at time of regeneration
- **Subscription Conversion Card**: Promotional card for standard subscription tier
  - Localized text for all conversion card elements
  - Feature highlights and pricing display
  - CTA buttons for subscription and browsing extensions
- **Component Configuration**: Analytics retention period setting (default 365 days)
- **Data Management**: Automatic cleanup of old analytics logs based on retention setting
- **Database Migration**: SQL schema for analytics tables with upgrade support

#### Changed
- Bumped version to 2.1.0
- Updated manifest to include SQL installation and update scripts
- Enhanced regeneration process to log detailed analytics data
- Analytics features require subscription access (placeholder implementation)

#### Technical
- Added `logRegenerationStart()` method to track regeneration initiation
- Added `logRegenerationComplete()` method to record results and processing time
- Added `logArticleStats()` method to maintain article regeneration counts
- Added `getRegenerationHistory()` method to fetch recent regenerations
- Added `getTopRegeneratedArticles()` method to get most regenerated articles
- Added `cleanupOldAnalytics()` method for automatic data retention
- Added `hasSubscriptionAccess()` placeholder method for future subscription integration
- Created database tables: `#__autometa_regeneration_log` and `#__autometa_article_stats`

### [1.2.1] - 2025-11-06

#### Added
- Complete UI redesign with 3-card interface
- Added statistics dashboard with progress bar
- Display current plugin settings in component
- Added "Empty Only" regeneration option to preserve existing meta descriptions
- Uses shared MetaDescriptionHelper for consistency with plugin

#### Changed
- Modernized for Joomla 5 with proper namespace structure

#### Fixed
- Fixed component class loading and autoloader registration

### [1.2.0] - 2025-11-01

#### Changed
- Updated for Joomla 5 compatibility
- Implemented modern service provider architecture

### [1.0.0] - 2024

#### Added
- Initial release
- Bulk regenerate meta descriptions for all articles
- CSRF protection and permission checks
- Error handling and logging
- Batch processing to handle large sites efficiently
