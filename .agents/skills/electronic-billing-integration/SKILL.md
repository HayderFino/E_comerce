---
name: electronic-billing-integration
description: Use this skill when working on the electronic billing (facturación electrónica) API integration for the virtual store.
---

# Electronic Billing Integration (Facturación Electrónica)

This skill provides guidelines and context for building the integration with the electronic billing service.

## Context
The application will be a virtual store. However, the current priority is to test and establish a connection with an external electronic billing service.

## Best Practices
- **API Wrapper**: Create a dedicated Service class (e.g., `App\Services\BillingService`) to encapsulate the HTTP requests using Laravel's `Http` facade.
- **Configuration**: Store API keys, base URLs, and environment specific credentials in the `.env` file and retrieve them through Laravel's `config()` helper (e.g., in `config/services.php`).
- **Testing**: Use `Http::fake()` to mock responses from the electronic billing API when writing Feature or Unit tests. Do not hit the real API in automated tests.
- **Error Handling**: Handle API errors gracefully (timeouts, 4xx/5xx responses) and log them using Laravel's Logging mechanism (`Log::error()`).

## Next Steps
- Identify the specific electronic billing provider (e.g., a local provider).
- Define the authentication method (OAuth, Bearer Token, API Key).
- Map out the payload required to generate an invoice.
