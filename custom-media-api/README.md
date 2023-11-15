# Custom Media API

**Custom Media API** is a WordPress plugin that empowers your website with a simple and secure RESTful API for media management. This plugin allows you to perform common media operations like uploading, retrieving, and deleting media files programmatically, making it easier to integrate media management into your applications and services.

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
- [License](#license)

## Introduction

Custom Media API is designed to enhance your WordPress site's capabilities by providing a custom API for media handling. With this plugin, you can manage media assets more efficiently and seamlessly, whether you're building a mobile app, a custom frontend, or integrating with other web services.

## Installation

Getting Custom Media API up and running is a breeze:

1. **Download**: First, grab the plugin ZIP file from the [GitHub repository](https://github.com/Akshat916/WP_API).

2. **Upload & Activate**: In your WordPress Admin Dashboard, navigate to "Plugins" and choose "Add New." Then, click "Upload Plugin" and select the downloaded ZIP file. Activate the "Custom Media API" plugin.

That's it! You're ready to explore the power of the Custom Media API.

## Usage

Once you've installed and activated the plugin, you can utilize its API endpoints to manage media programmatically. These endpoints allow you to interact with your media library without the need for manual interventions.

## Configuration

Custom Media API provides configuration options for fine-tuning your media management. You can access these settings through the WordPress Admin Dashboard under "Custom Media API Settings." Here's what you can configure:

- **Allowed File Extensions**: Specify which file extensions are permitted for media uploads.

- **Items Per Page**: Define the number of media items displayed per page.

- **Maximum File Size (MB)**: Set the maximum allowable file size in megabytes (MB).

Feel free to tailor these settings to your specific needs.

## Authentication

Custom Media API supports basic authentication, ensuring that your media management operations are secure and permission-based. Users can access the API using their WordPress credentials, and the plugin checks for their permissions before granting access.

## API Endpoints

Custom Media API offers the following API endpoints:

1. **Upload Media (`POST`)**: `/wp-json/custom/v1/upload-media`
   - Use this endpoint to upload media files to your WordPress site. It's a simple and secure way to add new media assets.

2. **Get Media (`GET`)**: `/wp-json/custom/v1/get-media`
   - Retrieve media items from your WordPress media library using this endpoint. Ideal for displaying media in your applications.

3. **Delete Media (`DELETE`)**: `/wp-json/custom/v1/delete-media`
   - Safely remove media items from the WordPress media library with this endpoint. Helps you keep your media library organized.

For in-depth guidance on using these endpoints, refer to the provided PHP code within the respective endpoint files: `upload-media.php`, `get-media.php`, and `delete-media.php`.

## License

This project is licensed under the [License Name], which you can find more details about in the [LICENSE.md](LICENSE.md) file.
