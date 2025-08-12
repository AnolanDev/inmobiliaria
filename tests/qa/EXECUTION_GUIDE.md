# 🚀 QA Execution Guide

## Quick Start Commands

### Run All QA Tests
```bash
# Complete test suite execution
php tests/qa/qa-runner.php

# Alternative with PHPUnit directly
php vendor/bin/phpunit --configuration tests/qa/Config/phpunit.qa.xml
```

### Run Specific Test Suites
```bash
# Security tests only
php tests/qa/qa-runner.php security

# Performance tests only  
php tests/qa/qa-runner.php performance

# Integration tests only
php tests/qa/qa-runner.php integration

# Unit tests only
php tests/qa/qa-runner.php unit
```

### Setup QA Environment
```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup test database
cp .env .env.testing
php artisan key:generate --env=testing

# 3. Run migrations for testing
php artisan migrate:fresh --env=testing

# 4. Seed QA test data
php artisan db:seed --class=Tests\\QA\\Data\\QATestDataSeeder --env=testing

# 5. Build frontend assets
npm run build
```

## Test Categories

### 🔒 Security Tests
**Purpose**: Verify system security and prevent vulnerabilities
**Location**: `tests/qa/Security/`
**Coverage**: 
- Authentication bypass attempts
- SQL injection prevention
- XSS attack mitigation
- CSRF protection
- File upload security
- Rate limiting

**Command**:
```bash
php tests/qa/qa-runner.php security
```

### ⚡ Performance Tests  
**Purpose**: Ensure system meets performance benchmarks
**Location**: `tests/qa/Performance/`
**Coverage**:
- Page load times (< 2s target)
- Database query efficiency
- Memory usage optimization
- Concurrent user handling
- Large dataset operations

**Command**:
```bash
php tests/qa/qa-runner.php performance
```

### 🔗 Integration Tests
**Purpose**: Verify modules work together correctly
**Location**: `tests/qa/Integration/`
**Coverage**:
- User-role-permission workflows
- Database transactions
- API endpoint interactions
- Service integrations
- Cascade operations

**Command**:
```bash
php tests/qa/qa-runner.php integration
```

### 🧪 Unit Tests
**Purpose**: Test individual functions and methods
**Location**: `tests/qa/Unit/`
**Coverage**:
- Model validations
- Business logic
- Helper functions
- Utility methods

**Command**:
```bash
php tests/qa/qa-runner.php unit
```

## Quality Gates

### ✅ Pass Criteria
- **Code Coverage**: ≥ 80% overall
- **Security Score**: No high-severity issues
- **Performance**: Page loads < 2s, API < 500ms
- **Functional**: All critical user flows work
- **Error Rate**: < 1% test failures

### ❌ Fail Criteria
- Security vulnerabilities detected
- Performance benchmarks exceeded
- Critical functionality broken
- Data integrity issues
- Authentication bypasses possible

## Test Data Personas

### QA Test Users
```
Super Admin: qa-super@test.com (Password: QAPassword123!)
Manager: qa-manager@test.com (Password: QAPassword123!)
Agent: qa-agent@test.com (Password: QAPassword123!)
Limited User: qa-limited@test.com (Password: QAPassword123!)
Inactive User: qa-inactive@test.com (Password: QAPassword123!)
```

### Edge Case Test Data
- Long names (255+ characters)
- Special characters (ñ, á, é, etc.)
- Inactive users
- Password expiry scenarios
- Multiple role assignments

## Reports and Dashboards

### HTML Dashboard
After running tests, open: `tests/qa/Reports/qa-dashboard.html`

**Features**:
- ✅ Overall success rate
- 📊 Performance metrics
- 🔍 Detailed test results
- 📈 Coverage reports
- 🔗 Quick navigation

### Coverage Reports
Individual coverage reports: `tests/qa/Reports/coverage/[suite]/index.html`

### CI/CD Integration
JUnit XML reports: `tests/qa/Reports/junit-[suite].xml`

## Performance Benchmarks

### Response Times
- **Dashboard Load**: < 1.5s
- **User Creation**: < 1s  
- **Role Assignment**: < 500ms
- **Permission Check**: < 10ms
- **Search Results**: < 1s

### Memory Usage
- **User Index (1000+ users)**: < 100MB
- **Role Operations**: < 20MB
- **Permission Checks**: < 5MB
- **Bulk Operations**: < 200MB

### Database Queries
- **N+1 Prevention**: < 10 queries per page
- **Slow Query Limit**: < 100ms average
- **Connection Pooling**: Efficient reuse

## Troubleshooting

### Common Issues

**1. Test Database Connection**
```bash
# Check database config
cat .env.testing | grep DB_

# Reset test database  
php artisan migrate:fresh --env=testing
```

**2. Memory Limits**
```bash
# Increase PHP memory limit
php -d memory_limit=512M tests/qa/qa-runner.php
```

**3. Permission Errors**
```bash
# Fix file permissions
chmod -R 755 tests/qa/Reports/
chmod +x tests/qa/qa-runner.php
```

**4. Browser Tests (if using Dusk)**
```bash
# Install Chrome driver
php artisan dusk:chrome-driver

# Run with headless Chrome
php artisan dusk --env=testing
```

## Continuous Integration

### GitHub Actions Example
```yaml
name: QA Testing
on: [push, pull_request]
jobs:
  qa-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install dependencies
        run: composer install
      - name: Run QA Tests
        run: php tests/qa/qa-runner.php
      - name: Upload coverage
        uses: actions/upload-artifact@v3
        with:
          name: coverage-report
          path: tests/qa/Reports/
```

## Best Practices

### 1. Test Isolation
- Each test should be independent
- Use database transactions
- Reset state between tests

### 2. Data Management
- Use factories for test data
- Avoid hardcoded values
- Clean up after tests

### 3. Performance Testing
- Run on production-like data volumes
- Monitor memory usage
- Test with concurrent users

### 4. Security Testing
- Test all input vectors
- Verify authorization checks
- Check for information leakage

### 5. Documentation
- Keep test documentation updated
- Document test data requirements
- Maintain troubleshooting guides

## Support

### Internal Documentation
- Architecture diagrams in `/docs/`
- API documentation in `/api-docs/`
- Database schema in `/database/docs/`

### External Resources
- Laravel Testing: https://laravel.com/docs/testing
- PHPUnit Documentation: https://phpunit.de/
- Security Testing Guide: https://owasp.org/

### Team Contacts
- QA Lead: [Your Name]
- DevOps Engineer: [Team Contact]
- Security Officer: [Security Contact]