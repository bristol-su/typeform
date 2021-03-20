# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Fixed
- portal-313: Implement rate limiting to avoid typeform API rate limits

## [1.1.20] - (06/03/2021)

### Fixed
- portal-305: Don't attempt typeform connection if information not given 

## [1.1.19] - (09/08/2020)

### Fixed
- portal-8: Reduced the space used by the responses table

## [1.1.18] - (11/05/2020)

### Changed
- Enable sorting on participant name and submitted at time for responses

## [1.1.17] - (06/05/2020)

### Added
- Enable filtering on response status

## [1.1.16] - (04/05/2020)

### Added
- Responses table now shows date time submitted at

## [1.1.15] - (04/05/2020)

### Changed
- Changed the comment column to a text from string

## [1.1.14] - (09/04/2020)

### Changed
- Reduce frequency of webhook syncing

## [1.1.13] - (09/04/2020)

### Changed
- Suppress 502 and 504 responses from Typeform

## [1.1.12] - (07/04/2020)

### Added
- Response Approved and Rejected events

## [1.1.11] - (06/04/2020)

### Fixed
- Cast the module instance, activity instance and user ID to an integer in payloads
- Rebind the module instance in the UpdateResposes job handle method (if necessary) not __construct

### Changed
- Webhook tag now includes the environment

## [1.1.10] - (06/04/2020)

### Fixed
- Bind module instance in job to ensure action can be dispatched

## [1.1.9] - (06/04/2020)

### Added
- Added comments on responses

## [1.1.8] - (04/04/2020)

### Changed
- Updated npm dependencies

## [1.1.7] - (03/04/2020)

### Added
- View responses permission to just view responses

### Changed
- View form permission now only hides the form, not the page

## [1.1.6] - (03/04/2020)

### Fixed
- Fixed handling error if no fields given by typeform

## [1.1.5] - (03/04/2020)

### Fixed
- Fixed handling error if no responses given by typeform

## [1.1.4] - (31/03/2020)

### Added
- Headers to file downloads for Vapor

## [1.1.3] - (31/03/2020)

### Fixed
- Binding the activity instance and module instance for an incoming webhook
- Only process an incoming webhook if the module instance ID matches
- Return a 400 response if the webhook does not match the module instance ID

## [1.1.2] - (31/03/2020)

### Fixed
- Response handler now updates responses/answers as well as saving them

## [1.1.1] - (31/03/2020)

### Changed
- Show N/A if the question is not given

## [1.1] - (31/03/2020)

### Added
- Field types - URL, File URL, Choices, Email, Phone Number
- Ability to approve and reject responses
- Completion Conditions based on number of approved or rejected responses

## [1.0.2] - (18/03/2020)

### Changed
- Updated Dependencies

## [1.0.1] - (12/03/2020)

### Added
- Registered events

## [1.0] - (12/03/2020)

### Added
- Initial Release

[Unreleased]: https://github.com/bristol-su/typeform/compare/v1.1.20...HEAD
[1.1.20]: https://github.com/bristol-su/typeform/compare/v1.1.19...v1.1.20
[1.1.19]: https://github.com/bristol-su/typeform/compare/v1.1.18...v1.1.19
[1.1.18]: https://github.com/bristol-su/typeform/compare/v1.1.17...v1.1.18
[1.1.17]: https://github.com/bristol-su/typeform/compare/v1.1.16...v1.1.17
[1.1.16]: https://github.com/bristol-su/typeform/compare/v1.1.15...v1.1.16
[1.1.15]: https://github.com/bristol-su/typeform/compare/v1.1.14...v1.1.15
[1.1.14]: https://github.com/bristol-su/typeform/compare/v1.1.13...v1.1.14
[1.1.13]: https://github.com/bristol-su/typeform/compare/v1.1.12...v1.1.13
[1.1.12]: https://github.com/bristol-su/typeform/compare/v1.1.11...v1.1.12
[1.1.11]: https://github.com/bristol-su/typeform/compare/v1.1.10...v1.1.11
[1.1.10]: https://github.com/bristol-su/typeform/compare/v1.1.9...v1.1.10
[1.1.9]: https://github.com/bristol-su/typeform/compare/v1.1.8...v1.1.9
[1.1.8]: https://github.com/bristol-su/typeform/compare/v1.1.7...v1.1.8
[1.1.7]: https://github.com/bristol-su/typeform/compare/v1.1.6...v1.1.7
[1.1.6]: https://github.com/bristol-su/typeform/compare/v1.1.5...v1.1.6
[1.1.5]: https://github.com/bristol-su/typeform/compare/v1.1.4...v1.1.5
[1.1.4]: https://github.com/bristol-su/typeform/compare/v1.1.3...v1.1.4
[1.1.3]: https://github.com/bristol-su/typeform/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/bristol-su/typeform/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/bristol-su/typeform/compare/v1.1...v1.1.1
[1.1]: https://github.com/bristol-su/typeform/compare/v1.0.2...v1.1
[1.0.2]: https://github.com/bristol-su/typeform/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/bristol-su/typeform/compare/v1.0...v1.0.1
[1.0]: https://github.com/bristol-su/typeform/releases/tag/v1.0
