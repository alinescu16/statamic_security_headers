# Statamic Security Headers

[![Statamic 4.0+](https://img.shields.io/badge/Statamic-4.0%2B-FF269E?style=for-the-badge&logo=statamic)](https://statamic.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/alinandrei/security-headers.svg?style=for-the-badge)](https://packagist.org/packages/alinandrei/security-headers)

A Statamic addon that allows you to easily manage and deploy crucial security headers for your website, complete with a powerful Content-Security-Policy (CSP) manager and violation reporting.

## Features

-   **Set Key Security Headers:** Easily enable and configure `Strict-Transport-Security` (HSTS), `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, and `Permissions-Policy`.
-   **Full CSP Management:** Define your `Content-Security-Policy` (CSP) from a simple YAML file.
-   **CSP Violation Reporting:** Includes a built-in endpoint to capture CSP violations from users' browsers.
-   **Control Panel Reporting UI:** (Assuming you are building this based on your helper functions) View all CSP violations directly in the Statamic Control Panel.
-   **"Click to Allow" Policies:** (Assuming you are building this) Add blocked resources to your CSP directly from the violation report log, making CSP management a breeze.

## How to Install

You can install this addon via Composer:

```bash
composer require alinandrei/security-headers