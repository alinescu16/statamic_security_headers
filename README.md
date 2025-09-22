# Statamic Security Headers

[![Statamic 4.0+](https://img.shields.io/badge/Statamic-4.0%2B-FF269E?style=for-the-badge&logo=statamic)](https://statamic.com)

A Statamic addon that allows you to easily manage and deploy crucial security headers for your website, complete with a powerful Content-Security-Policy (CSP) manager and violation reporting.

## Features

-   **Set Key Security Headers:** Easily enable and configure `Strict-Transport-Security` (HSTS), `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, and `Permissions-Policy`.
-   **Full CSP Management:** Define your `Content-Security-Policy` (CSP) directly from the dashboard.
-   **CSP Violation Reporting:** Includes a built-in endpoint to capture CSP violations from users' browsers. Supported integrations are currently: Sentry; Comming up: RayGun, c/Side.
-   **"Click to Allow" Policies:** Add blocked resources to your CSP directly from the statamic dashboard. Carefull! Always review the CSP directives and values before saving your settings.
-   **Modern implementation** using Contracts, dynamically injected clients for different reporting platforms, Service containers and Middleware implementation for adding the headers to the responses.
-   **One Time** purchase for a single domain.

