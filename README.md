# Custom Media API for WordPress

**Custom Media API** is a versatile WordPress plugin designed to simplify and secure media management through a RESTful API. This plugin empowers you to perform common media operations programmatically, including uploading, retrieving, and deleting media files. Whether you're building a mobile app, custom frontend, or integrating with other services, Custom Media API enhances your media management capabilities.

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
- [DXP Settings](#dxp-settings)
- [Update CSS](#update-css)
- [Update JS](#update-js)
- [License](#license)

## Introduction

Enhance your WordPress site with Custom Media API, a plugin designed to optimize media handling. Whether you're building a mobile app, custom frontend, or integrating with other web services, this plugin empowers you with a custom API for efficient media management.

## Installation

Getting started with Custom Media API is quick and easy:

1. **Download**: Obtain the plugin ZIP file from the [GitHub repository](https://github.com/Akshat916/WP_API).

2. **Upload & Activate**: In your WordPress Admin Dashboard, navigate to "Plugins," choose "Add New," click "Upload Plugin," and select the downloaded ZIP file. Activate the "Custom Media API" plugin.

Now you're ready to explore the capabilities of Custom Media API.

## Usage

Once installed and activated, leverage the plugin's API endpoints to programmatically manage media. These endpoints allow you to interact with your media library seamlessly, eliminating the need for manual interventions.

## Configuration

Custom Media API offers configuration options for fine-tuning your media management. Access these settings in the WordPress Admin Dashboard under "Custom Media API Settings." Customize the following:

- **Allowed File Extensions**: Specify permitted file extensions for media uploads.
- **Items Per Page**: Define the number of media items displayed per page.
- **Maximum File Size (MB)**: Set the maximum allowable file size in megabytes (MB).

Tailor these settings to meet your specific requirements.

## Authentication

Ensure secure and permission-based media management with Custom Media API's basic authentication support. Users can access the API using their WordPress credentials, with the plugin verifying their permissions before granting access.

## API Endpoints

Custom Media API provides the following API endpoints:

1. **Upload Media (`POST`)**: `/wp-json/custom/v1/upload-media`
   - Upload media files securely to your WordPress site with this endpoint.

2. **Get Media (`GET`)**: `/wp-json/custom/v1/get-media`
   - Retrieve media items from your WordPress media library programmatically.

3. **Delete Media (`DELETE`)**: `/wp-json/custom/v1/delete-media`
   - Safely remove media items from the WordPress media library using this endpoint.

4. **Update CSS (`POST`)**: `/wp-json/custom/v1/update-css`
   - Update CSS data from a custom API endpoint.

5. **Update JS (`POST`)**: `/wp-json/custom/v1/update-js`
   - Update JS data from a custom API endpoint.

For detailed guidance on using these endpoints, refer to the provided PHP code within the respective endpoint files: `upload-media.php`, `get-media.php`, `delete-media.php`, `update-css.php`, and `update-js.php`.

## DXP Settings

Custom Media API integrates with DXP offers configurable settings. Access these settings under "DXP Configuration" in the WordPress Admin Dashboard.

