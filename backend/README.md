# API Backend Documentation

## Overview
This is the backend API service for the AppGestion dashboard application. It serves as a middleware between the frontend application and the main API server, providing additional security, rate limiting, and error handling capabilities.

## Features
- Robust error handling and logging
- Rate limiting to prevent API abuse
- Secure API communication with SSL verification
- Configurable settings via config file
- Professional logging system

## Directory Structure
```
backend/
├── config/
│   └── config.php         # Configuration settings
├── src/
│   ├── ErrorHandler.php   # Error handling functionality
│   └── RateLimiter.php    # Rate limiting implementation
├── storage/
│   ├── logs/             # Error logs
│   └── rate_limit/       # Rate limiting data
├── api.php               # Main API handler
├── aut.php              # Authentication handler
├── common.php           # Common functionality
├── dashboard.php        # Dashboard specific endpoints
└── README.md           # This documentation
```

## Configuration
All configuration settings are stored in `config/config.php`. Key settings include:

- API server configuration
- Rate limiting parameters
- Error reporting settings

## Rate Limiting
The API implements rate limiting to prevent abuse:

- Default: 100 requests per hour per IP
- Headers returned:
  - `X-RateLimit-Limit`: Maximum requests per window
  - `X-RateLimit-Remaining`: Remaining requests in current window
  - `X-RateLimit-Reset`: Time when the rate limit resets

## Error Handling
Comprehensive error handling system:

- Detailed error logging in production
- Sanitized error messages to clients
- All errors logged to `storage/logs/error.log`

## Security
- SSL verification enabled for API communications
- Authentication token required for all requests
- Rate limiting per IP address
- Sanitized error messages in production

## API Endpoints

### Dashboard Endpoints
Endpoint: `/dashboard/{method}`
Methods available:
- GET: Retrieve dashboard data
- POST: Update dashboard data

### Authentication
All requests must include:
- `Authorization` header with API token
- `Distribuidor` header with distributor ID

## Development Setup
1. Ensure PHP 7.4+ is installed
2. Create required directories:
   ```bash
   mkdir -p storage/logs storage/rate_limit
   chmod 755 storage/logs storage/rate_limit
   ```
3. Copy `config/config.php.example` to `config/config.php`
4. Update configuration settings as needed

## Error Codes
- 200: Success
- 429: Rate limit exceeded
- 500: Internal server error
- 401: Unauthorized
- 403: Forbidden

## Best Practices
1. Always check rate limit headers
2. Implement exponential backoff for retries
3. Handle errors gracefully
4. Keep API tokens secure

## Maintenance
- Monitor `storage/logs/error.log` for issues
- Regularly clean up old rate limit data
- Keep dependencies updated
- Review API token security regularly

## Support
For support or bug reports, please contact the development team.
