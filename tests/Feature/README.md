# Feature Tests

This directory contains feature tests for the Laravel application.

## JWT Authentication Tests (`JWTAuthTest.php`)

Comprehensive test suite for JWT authentication functionality covering all major authentication endpoints and scenarios.

### Test Coverage

#### Registration Tests

-   ✅ Successful user registration with valid data
-   ✅ Validation of required fields (first_name, last_name, email, password)
-   ✅ Email format validation
-   ✅ Password confirmation validation
-   ✅ Password minimum length validation (8 characters)
-   ✅ Duplicate email prevention
-   ✅ String field length validation (max 255 characters)

#### Login Tests

-   ✅ Successful login with valid credentials
-   ✅ Failed login with invalid email (returns 422 with validation error)
-   ✅ Failed login with invalid password (returns 422 with validation error)
-   ✅ Validation of required fields for login
-   ✅ Email format validation for login

#### User Profile Tests

-   ✅ Get authenticated user profile with valid token
-   ✅ Authentication required to access user profile (returns 401)
-   ✅ Invalid token handling (returns 401)

#### Token Refresh Tests

-   ✅ Authentication required to refresh token (returns 401)
-   ✅ Invalid token handling for refresh (returns 401)
-   ⚠️ **Note**: Token refresh functionality test is skipped due to JWT refresh issues in test environment

#### Rate Limiting Tests

-   ⚠️ **Note**: Rate limiting test is skipped due to JWT refresh dependency

#### Full Authentication Flow Tests

-   ✅ Complete basic authentication flow (register → get profile)

### Test Structure

The tests are organized using Pest's `describe` and `it` functions for better readability:

```php
describe('JWT Authentication', function () {
    describe('Registration', function () {
        it('can register a new user successfully', function () {
            // Test implementation
        });
    });
});
```

### Key Features Tested

1. **Data Validation**: All form validation rules are tested
2. **Authentication Flow**: Complete user registration and login process
3. **Token Management**: JWT token generation and validation
4. **Error Handling**: Proper error responses for invalid requests
5. **Security**: Authentication requirements for protected endpoints
6. **Database Operations**: User creation and retrieval

### Test Data

Tests use Laravel's factory system to create test users:

```php
$user = User::factory()->create([
    'email' => 'john.doe@example.com',
    'password' => Hash::make('password123'),
]);
```

### API Endpoints Tested

-   `POST /api/auth/register` - User registration
-   `POST /api/auth/login` - User login
-   `GET /api/auth/me` - Get user profile
-   `POST /api/auth/refresh` - Refresh JWT token

### Running the Tests

```bash
# Run all JWT authentication tests
php artisan test tests/Feature/JWTAuthTest.php

# Run with verbose output
php artisan test tests/Feature/JWTAuthTest.php --verbose

# Run specific test
php artisan test tests/Feature/JWTAuthTest.php --filter="can register a new user successfully"
```

### Known Issues

1. **JWT Token Refresh**: The token refresh functionality has issues in the test environment due to token handling differences between test and production environments. This affects:

    - Token refresh tests
    - Rate limiting tests that depend on refresh functionality

2. **Test Environment**: Some JWT operations behave differently in the test environment compared to production.

### Future Improvements

1. Fix JWT refresh issues in test environment
2. Add more edge case tests
3. Add integration tests with external services
4. Add performance tests for rate limiting
5. Add tests for token expiration scenarios
