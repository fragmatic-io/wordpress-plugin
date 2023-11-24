# Custom Media API for WordPress

**Custom Media API** is a versatile WordPress plugin designed to simplify and secure media management through a RESTful API. This plugin empowers you to perform common media operations programmatically, including uploading, retrieving, and deleting media files. Whether you're building a mobile app, custom frontend, or integrating with other services, Custom Media API enhances your media management capabilities.

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
  - [Upload Media (`POST`)](#1-upload-media-post)
  - [Get Media (`GET`)](#2-get-media-get)
  - [Delete Media (`DELETE`)](#3-delete-media-delete)
  - [Update CSS (`POST`)](#4-update-css-post)
  - [Update JS (`POST`)](#5-update-js-post)
- [ControlTower Settings](#controltower-settings)
- [License](#license)

## Introduction

Enhance your WordPress site with Custom Media API, a plugin designed to optimize media handling. Whether you're building a mobile app, custom frontend, or integrating with other web services, this plugin empowers you with a custom API for efficient media management.

## Installation

Getting started with Custom Media API is quick and easy:

1. **Download**: Obtain the plugin ZIP file.

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

### 1. Upload Media (`POST`)

Upload media files securely to your WordPress site.

- **Endpoint:** `https://yourwordpresssite.com/?rest_route=/custom/v1/upload-media`
- **Method:** `POST`
- **Parameters:**
  - `file` (Request body) - The file to upload.
  - `alt_text` (Request body) - Alt text for the media (optional).
  - `caption` (Request body) - Caption for the media (optional).
- **Example:**
  ```http
  POST https://yourwordpresssite.com/?rest_route=/custom/v1/upload-media
  Content-Type: multipart/form-data

  file=@"/path/to/your/file.jpg"
  alt_text= "Alt text for the media"
  caption= "Caption for the media"

### 2. Get Media (`GET`)

Retrieve media items from your WordPress media library.

- **Endpoint:** `https://yourwordpresssite.com/?rest_route=/custom/v1/get-media`
- **Method:** `GET`
- **Parameters:**
  - `id` (Query parameter) - ID of the media item (optional).
  - `page` (Query parameter) - Page number for pagination (optional).
- **Examples:**
  - Retrieve all media: `GET https://yourwordpresssite.com/?rest_route=/custom/v1/get-media`
  - Retrieve specific media by ID: `GET https://yourwordpresssite.com/?rest_route=/custom/v1/get-media&id=123`
  - Paginate through media: `GET https://yourwordpresssite.com/?rest_route=/custom/v1/get-media&page=2`

### 3. Delete Media (`DELETE`)

Safely remove media items from the WordPress media library.

- **Endpoint:** `https://yourwordpresssite.com/?rest_route=/custom/v1/delete-media`
- **Method:** `DELETE`
- **Parameters:**
  - `id` (Query parameter) - ID of the media item to delete.
- **Example:**
  ```http
  DELETE https://yourwordpresssite.com/?rest_route=/custom/v1/delete-media&id=456

### 4. Update CSS (`POST`)

Update CSS data from a custom API endpoint.

- **Endpoint:** `https://yourwordpresssite.com/?rest_route=/custom/v1/update-css`
- **Method:** `POST`
- **Parameters:**
  - No parameters required.


### 5. Update JS (`POST`)

Update JS data from a custom API endpoint.

- **Endpoint:** `https://yourwordpresssite.com/?rest_route=/custom/v1/update-js`
- **Method:** `POST`
- **Parameters:**
  - No parameters required.


For detailed guidance on using these endpoints, refer to the provided PHP code within the respective endpoint files: `upload-media.php`, `get-media.php`, `delete-media.php`, `update-css.php`, and `update-js.php`.

## ControlTower Settings

Custom Media API integrates with ControlTower offers configurable settings. Access these settings under "ControlTower Configuration" in the WordPress Admin Dashboard.
