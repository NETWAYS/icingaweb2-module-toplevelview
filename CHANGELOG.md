# Changelog

## [v1.1.1](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v1.1.1) (unreleased)

- Add handling for overdue states
- Improve PHP type hints and some code simplifications
- Modernize Icinga JavaScript, replace some jQuery with native

## [v1.1.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v1.1.0) (2026-07-06)

**Implemented enhancements:**

- Add custom queries for hostgroup and servicegroup to query downtimes, this enables downtime colors and badges
- Make the badge and tile color independent from each other, this allows for more customizations

## [v1.0.1](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v1.0.1) (2026-02-20)

**Implemented enhancements:**

- PHP 8.5 Support

## [v1.0.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v1.0.0) (2024-12-05)

**Implemented enhancements:**

- Add restrictions `toplevelview/filter/view` and `toplevelview/filter/edit`
- Add Service Groups as TLV elements
- Add option to toggle `notification_enabled` behavior
- Add validation for view filenames (restricted characters are: `! @ # $ % ^ & * / \ ( )`)
- Add CLI Command to clean up backups
- Improve error handling for YAML editor
- Improve CSS for dark and light mode

## [v0.4.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.4.0) (2024-07-16)

**Breaking changes:**

- Remove support for the Icinga Web monitoring module

Future versions will only use IcingaDB Web.

- Remove `notification_periods` and `ignored_notification_periods` options

These options changed the state behavior of the module significantly (when compared to Icinga Web)
and thus caused some confusion/limitations.

- Rename `host_never_unhandled` to `override_host_problem_to_handled`

Tried to make this options clearer to understand.

**Implemented enhancements:**

- Add support for Icinga DB
- Views use the display names for object if configured
- Improve the View's headers styling
- Improve handling of long View names (uses ellipsis now)

## [v0.3.4](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.3.4) (2024-06-12)

This will be the final version with support for
Icinga Web monitoring module. Future versions will use IcingaDB Web.

**Implemented enhancements:**

- Improve CSS for dark/light theme
- Add confirmation dialog to deletion button
- Add Icinga Web CSS classes to delete and cancel button
- Update codemirror5 to latest version and add code folding
- Update README to include php-yaml requirement

**Fixed bugs:**

- Fix CSS issue with node collapse icon
- Change unsaved work warning position to avoid breaking the layout

## [v0.3.3](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.3.3) (2021-09-10)

**Implemented enhancements:**

- Rename collapse feature for Icinga Web >= 2.7 ([lazyfrosch](https://github.com/lazyfrosch))

## [v0.3.2](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.3.2) (2021-03-19)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/v0.3.1...v0.3.2)

**Implemented enhancements:**

- legacy: Add cleanup and IDO migration tool [\#43](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/43) ([lazyfrosch](https://github.com/lazyfrosch))

**Merged pull requests:**

- Fix typos [\#39](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/39) ([sklaes](https://github.com/sklaes))
- Update 02-Behavior.md [\#38](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/38) ([micheledallatorre](https://github.com/micheledallatorre))

## [v0.3.1](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.3.1) (2019-03-06)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/v0.3.0...v0.3.1)

**Fixed bugs:**

- Implement Notification Period ignoring for TLVServiceNode [\#36](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/36) ([lazyfrosch](https://github.com/lazyfrosch))
- HostgroupsummaryQuery: Ignore hostgroups that are empty [\#35](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/35) ([lazyfrosch](https://github.com/lazyfrosch))

## [v0.3.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.3.0) (2019-02-28)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/v0.2.1...v0.3.0)

**Implemented enhancements:**

- Allow ignoring a list of notification periods for handled problems [\#34](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/34) ([lazyfrosch](https://github.com/lazyfrosch))

**Closed issues:**

- You need the PHP extension "yaml" in order to use TopLevelView \(rh-php71\) [\#33](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/33)

## [v0.2.1](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.2.1) (2018-02-13)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/v0.2.0...v0.2.1)

**Fixed bugs:**

- UTC timeperiod calculation is wrong [\#29](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/29)

**Merged pull requests:**

- Don't use UTC timestamps for calculating timeperiods [\#30](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/30) ([lazyfrosch](https://github.com/lazyfrosch))

## [v0.2.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.2.0) (2018-01-25)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/v0.1.0...v0.2.0)

**Implemented enhancements:**

- style: Update handled colors with material design [\#28](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/28) ([lazyfrosch](https://github.com/lazyfrosch))

**Fixed bugs:**

- Undefined index: unknown coming from TLVServiceNode [\#22](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/22)

**Closed issues:**

- Apply monitoring filter to views [\#25](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/25)
- Document requirement for Icingaweb2 \>= 2.5.0 [\#23](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/23)
- You need the PHP extension "yaml" in order to use TopLevelView [\#21](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/21)

## [v0.1.0](https://github.com/Icinga/icingaweb2-module-toplevelview/tree/v0.1.0) (2017-10-17)

[Full Changelog](https://github.com/Icinga/icingaweb2-module-toplevelview/compare/a7bf9bee8ea768a7ba7afe7191f11f221475f1b1...v0.1.0)

**Implemented enhancements:**

- Add documentation [\#15](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/15)
- Add badges [\#8](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/8)
- Activate auto-reload and show counter [\#6](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/6)
- Add caching layer [\#3](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/3)
- Integrate notification\_period behavior [\#2](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/2)
- Add documentation [\#19](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/19) ([lazyfrosch](https://github.com/lazyfrosch))
- Add caching layer [\#9](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/9) ([lazyfrosch](https://github.com/lazyfrosch))

**Fixed bugs:**

- host\_never\_unhandled should affect service\_handled [\#20](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/20)
- Host down should be visible [\#7](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/7)
- Auto create missing config dir [\#5](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/5)

**Closed issues:**

- Make notification\_period an option [\#18](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/18)
- Fix single services in convert [\#17](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/17)
- Add de\_DE translation [\#16](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/16)
- Config option for host handled [\#14](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/14)
- Add docker test and dev environment [\#13](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/13)
- Config Files should be created with group write permission [\#12](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/12)
- SOFT states should be ignored [\#11](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/11)
- Convert in downtime and notifications disabled to a downtime state [\#10](https://github.com/Icinga/icingaweb2-module-toplevelview/issues/10)

**Merged pull requests:**

- Add CLI convert tool for sqlite database [\#1](https://github.com/Icinga/icingaweb2-module-toplevelview/pull/1) ([lazyfrosch](https://github.com/lazyfrosch))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
