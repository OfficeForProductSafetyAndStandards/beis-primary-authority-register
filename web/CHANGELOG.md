# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [20.2.1]
### Added
- Role assignment so that the administrator role can only be assigned by administrators.

### Fixed
- Removed sporadic issue downloading the Help Desk CSV report.

## [20.1.0]
### Changed
- Updated the public primary authority search with partnership type column and better search.
- Public register now has more search options and displays a 'no results' message if no partnerships were found.
- Partnership type has been added to all partnership search lists.

### Fixed
- Added the correct completion text at the end of the partnership confirmation process.
- Accessibility issues showing complex labels to screenreaders
- Forms where multiples can now be added show '(optional)' help text for all but the first item.
- Hidden menu links disabled for screenreader.
- Pagination now validates as (x)html.

## [20.0.1] - 2019-07-05
### Added
- GDPR agreement required after registration.
- Profile update capabilities allows review of user's.
- A password policy for all users that requires new passwords to be a minimum of 8 characters long, not contain all lowercase or all uppercase characters, and not use commonly used words or sequences of characters.

### Changed
- Performance improvements made by removing relationships to deleted data.

## [19.0.0] - 2019-06-25
### Added
- Added journeys for helpdesk users to restore mistakenly revoked partnerships.
### Changed
- Updated display of enforcement actions to always include attachments.
### Fixed
- Partnerships now show up immediately in a users dashboard when first created.
- Resolved missing relationships between users and their relevant enforcements and partnerships.

## [18.3.2] - 2019-06-10
### Fixed
- Resolved issues blocking enforcements that can be referred.

## [18.3.1] - 2019-06-07
### Added
- Authorities can now complete business details.
- New links on partnership review screen to allow details to be changed.
### Fixed
- Allow legal entiites to be changed from the review page under rare use cases when none are present.
- Partnerships can no longer be transitioned back to 'Confirmed by the organisation' once they have been nominated.
- Notifications of enforcement review are now being sent to the correct enforcement officer.
- Resolved issues blocking the creation of new partnerships.

## [18.2.0] - 2018-06-06
### Added
- Login now works both in lower and upper case.
- Added missing pages for advice and inspeciton plans when searching for a partnership.
### Changed
- User account link now goes to the dashboard.
### Fixed
- Corrected issues adding multiple enforcement actions.
- Improved general accessibility issues.

## [18.1.0] - 2018-06-02
### Changed
- Improved dashboard for users that have been removed from authorities or organisations.
### Fixed
- Corrected default vaue for 'Covered by Inspection' radio options.
- List formatting removed from email messages.
- Resolved issues with a recent upgrade to invitations.
### Removed
- Filtered deleted items from the helpdesk partnership list.

## [18.0.0] - 2018-05-30
### Added
- New CSV report page for the helpdesk.
### Changed
- Updated instructional text to help users upload attachements.
- Enforcement journeys given UI improvements and multiple fixes to allow more partnerships to be enforced, improve notifications and reduce errors.
### Fixed
- Removed duplicate email signature from some email notifications.
- Resolved failure of invite emails to new users during partnership creation.

## [1.0.0] - 2017-08-22
### Added
- Transition site released
