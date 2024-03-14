# Changelog

## [1.0.3](https://github.com/latomate07/laraversion/releases/tag/v1.0.3) - 2024-03-14

### Added

- Add and improved UI and UX GUI to be able to see model versions, compare them, and restore them if necessary.
- Create and publish a command to publish the laraversion interface to the project (by default, laraversion is not supplied with the interface)
- Add the option of choosing whether or not to load the GUI.
- Add middleware option for laraversion GUI routes

### Fixed

- Fix restoration functions (revertToVersion and restoreVersion) that recreates a new version, which it shouldn't