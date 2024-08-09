### 1. Plugin Description

**Plugin Name**: Posts Scanner

**Description**:
The "Posts Scanner" plugin is designed to scan all public posts and pages on a WordPress site. It updates the post meta with the current timestamp, ensuring regular maintenance and monitoring of content updates. This plugin offers both a user-friendly interface and a CLI command for scanning.

### 2. User Documentation and Usage

- **Admin Interface**:
  - Navigate to the Posts Scanner page in the WordPress admin menu.
  - Click the "Scan Posts" button to initiate the scanning process. This will update the meta information for all public posts and pages.

- **CLI Command**:
  - You can also run the scan using WP-CLI with the following command:
    ```bash
    wp posts_scanner scan --post_type=page
    ```
  - This command will perform the same operation as the button in the admin interface, updating the post meta with the current timestamp.

### 3. Technical Documentation

- **Src**:
  - Contains source files for the React components and utility functions used in the admin interface.

- **Core**:
  - Contains core classes for the plugin, such as `class-endpoint.php`, `class-loader.php`, and others that manage the core functionality of the plugin.

- **App**:
  - Contains application-specific code, including admin page classes, service classes, and CLI command classes.

**Key Classes and Files**:
- **Admin Interface**:
  - `app/pages/admin/class-post-maintenance.php`: Manages the admin page where the scan is initiated.
  - `src/pages/posts-maintenance.jsx`: React component for the admin page interface.

- **Services**:
  - `app/services/class-posts-scan-service.php`: Contains the logic for scanning posts and updating their meta information.
  - `app/services/class-scheduler-service.php`: Manages scheduling tasks related to post scanning.

- **CLI Command**:
  - `app/commands/class-posts-scan-cli-command.php`: Defines the WP-CLI command for scanning posts.

- **Endpoints**:
  - `app/endpoints/v1/class-posts-scan.php`: Defines the REST API endpoint for initiating the post scan.

**Technical Implementation**:
- The plugin uses a combination of PHP and JavaScript. The PHP classes handle the backend functionality, such as defining REST API endpoints, WP-CLI commands, and interacting with the database.
- The admin interface is built using React, with Webpack used to bundle the JavaScript files.
- The plugin follows WordPress coding standards, with configurations provided for PHP CodeSniffer and PHPUnit.

### 4. Technologies Used

- **PHP**: The primary language used for the backend functionality.
- **JavaScript**: Used for the admin interface, primarily with React.
- **Composer**: For managing PHP dependencies.
- **NPM**: For managing JavaScript dependencies.
- **Webpack**: For bundling JavaScript files.
- **PHPUnit**: For running unit tests.
- **WP-CLI**: For providing command-line functionality.


# Installation

## Composer
Install composer packages
`composer install`

## Build Tasks (npm)
Everything should be handled by npm.

Install npm packages
`npm install`

| Command              | Action                                                |
|----------------------|-------------------------------------------------------|
| `npm run watch`      | Compiles and watch for changes.                       |
| `npm run compile`    | Compile production ready assets.                      |
| `npm run build`      | Compile development assets.                            |
