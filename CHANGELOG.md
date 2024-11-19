# Changelog

## [5.0.0]
- ILIAS 8 support

## [4.0.0]
- ILIAS 7 support
- Remove ILIAS 5.4 support

## [3.0.1]
- Only show tabs if course has tabs (Hide tabs create sub object)

## [3.0.0]
- ILIAS 6 support
- Core main menu
- Integrate 'CertificateEvents'
- Separate legacy cron job to `CertificateCron` wrapper plugin
- Remove run cron job from plugin config (It's now possible to run in ILIAS core cron GUI)
- Remove ILIAS 5.3 support
- Min. PHP 7.0

## [2.1.0]
- Feature: "Show all certificate versions" definition setting
- Feature: Add "My Certificates" tab in courses

## [2.0.2]
- Bugfix: error in definition form when lp is inactive
- Bugfix: Open certficate tab crashes if course is offline

## [2.0.1]
- Bugfix: fixed nl2br error (default language not set)

## [2.0.0]
- Feature: Participation Certificates

## [1.9.1]
- Bugfix: signatures with long course titles didn't work
- Change: switched from hyphen to underscore in filename

## [1.9.0]
- Change: filename order changed to user_name + date + course title
- Improvement: filename replaces umlauts with 'ae', 'ue' and 'oe'
- Improvement: allow cyrillic characters
- Bugfix: ILIAS_HTTP_PATH not set in cron context
- Bugfix: fixed excel export

## [1.8.0]
- Feature: new placeholder 'CATEGORY_TITLE' is filled with the title of the repository category containing the certificate course

## [1.7.0]
- Feature: Successor Course - define a course to which members will be subscribed automatically after the 'valid to' date of a certificate is reached
- Feature: Start the Certificate Cron Job manually via the Plugin Administration
- Improvement: Exception Handling for better Error Logging
- Updated Documentation


## [1.6.0]
- Support for ILIAS 5.2.x - 5.3.x

## [1.5.0]
- Feature: Introduced Participants Table to manually create certificates and set validity dates.
- Support for ILIAS 5.0.x - 5.1.x

## [1.2.0]
- Support for ILIAS 4.3.x - 5.1.x
