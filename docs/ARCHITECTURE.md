# Architecture Guide

## Clean Architecture Principles

Risk Management Suite Lite mengikuti prinsip Clean Architecture dengan fokus pada:

1. **Independence of Frameworks**: Business logic tidak depend pada framework
2. **Testability**: Business rules dapat ditest tanpa UI, DB, atau external services
3. **Independence of UI**: UI dapat berubah tanpa affect business logic
4. **Independence of DB**: Database layer dapat diganti tanpa affect business logic
5. **Independence of Agencies**: Business rules tidak depend pada external agencies

## Layered Architecture

```
┌─────────────────────────────────────────────────────┐
│                 Presentation Layer                   │
│          (Controllers, Views, API Resources)        │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│                 Application Layer                    │
│            (Services, Form Requests)                │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│                  Domain Layer                        │
│         (Entities, Value Objects, Repositories)     │
└────────────────┬────────────────────────────────────┘
                 │
┌────────────────▼────────────────────────────────────┐
│                Infrastructure Layer                  │
│       (Database Models, External Services)          │
└─────────────────────────────────────────────────────┘
```

## Directory Structure

### `/app/Domains`

Setiap domain memiliki struktur yang sama:

```
Domains/
├── Risk/
│   ├── Entities/          # Pure business logic (no framework)
│   │   └── Risk.php
│   ├── ValueObjects/      # Immutable values
│   │   ├── RiskScore.php
│   │   └── RiskLevel.php
│   ├── Repositories/      # Interface definitions
│   │   └── RiskRepositoryInterface.php
│   ├── Services/          # Business use cases
│   │   ├── CreateRiskService.php
│   │   ├── UpdateRiskService.php
│   │   ├── CalculateRiskScoreService.php
│   │   └── ListRisksService.php
│   ├── Events/            # Domain events
│   │   └── RiskCreatedEvent.php
│   └── Exceptions/        # Domain exceptions
│       └── RiskNotFoundException.php
├── Incident/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/
│   ├── Services/
│   ├── Events/
│   └── Exceptions/
├── CorrectiveAction/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/
│   ├── Services/
│   ├── Events/
│   └── Exceptions/
├── Approval/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/
│   ├── Services/
│   ├── Events/
│   └── Exceptions/
├── KPI/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/
│   ├── Services/
│   ├── Events/
│   └── Exceptions/
└── Shared/
    ├── ValueObjects/      # Shared value objects
    │   ├── UserId.php
    │   ├── CompanyId.php
    │   └── Status.php
    └── Exceptions/        # Shared exceptions
```

### `/app/Http`

```
Http/
├── Controllers/
│   ├── Api/               # API Controllers
│   │   ├── RiskController.php
│   │   ├── IncidentController.php
│   │   └── ...
│   └── Web/               # Web Controllers
│       ├── RiskController.php
│       ├── IncidentController.php
│       └── DashboardController.php
├── Middleware/
│   ├── CheckCompany.php
│   ├── CheckRole.php
│   └── LogActivity.php
├── Requests/              # Form Requests (Validation)
│   ├── StoreRiskRequest.php
│   ├── UpdateRiskRequest.php
│   └── ...
└── Resources/             # API Resources (Serialization)
    ├── RiskResource.php
    ├── IncidentResource.php
    └── ...
```

### `/app/Models`

```
Models/
├── User.php               # Eloquent Model
├── Company.php
├── Department.php
├── Risk.php
├── Incident.php
├── CorrectiveAction.php
├── Approval.php
├── KPI.php
├── ActivityLog.php
└── Notification.php
```

### `/app/Repositories`

```
Repositories/
├── Contracts/             # Interface definitions
│   ├── RiskRepositoryContract.php
│   ├── IncidentRepositoryContract.php
│   └── ...
└── Eloquent/              # Eloquent implementations
    ├── RiskRepository.php
    ├── IncidentRepository.php
    └── ...
```

### `/app/Services`

```
Services/
├── RiskService.php
├── IncidentService.php
├── CorrectiveActionService.php
├── ApprovalService.php
├── KPIService.php
├── ExportService.php      # Export functionality
├── ImportService.php      # Import functionality
└── NotificationService.php
```

### `/database/migrations`

```
migrations/
├── 2024_01_01_000000_create_companies_table.php
├── 2024_01_02_000000_create_departments_table.php
├── 2024_01_03_000000_create_users_table.php
├── 2024_01_04_000000_create_risks_table.php
├── 2024_01_05_000000_create_incidents_table.php
├── 2024_01_06_000000_create_corrective_actions_table.php
├── 2024_01_07_000000_create_approvals_table.php
├── 2024_01_08_000000_create_kpis_table.php
├── 2024_01_09_000000_create_activity_logs_table.php
└── 2024_01_10_000000_create_notifications_table.php
```

## Design Patterns Used

### 1. Repository Pattern

**Alasan**: Abstraksi data access layer, memudahkan switching database atau testing

```php
interface RiskRepositoryContract
{
    public function store(array $data): Risk;
    public function update(string $id, array $data): Risk;
    public function getById(string $id): ?Risk;
    public function getByCompany(string $companyId): Collection;
}

class RiskRepository implements RiskRepositoryContract
{
    // Implementation using Eloquent
}
```

### 2. Service Layer Pattern

**Alasan**: Memisahkan business logic dari controllers, reusable untuk API dan Web

```php
class CreateRiskService
{
    public function __construct(
        private RiskRepositoryContract $repository
    ) {}

    public function execute(CreateRiskDTO $dto): Risk
    {
        // Business logic here
    }
}
```

### 3. Data Transfer Object (DTO) Pattern

**Alasan**: Type-safe parameter passing, validation separation

```php
class CreateRiskDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $likelihood,
        public int $impact,
    ) {}
}
```

### 4. Value Object Pattern

**Alasan**: Encapsulate complex business logic, immutability

```php
class RiskScore
{
    private function __construct(
        private int $score
    ) {
        if ($score < 0 || $score > 25) {
            throw new InvalidRiskScoreException();
        }
    }

    public static function create(int $likelihood, int $impact): self
    {
        return new self($likelihood * $impact);
    }

    public function getValue(): int
    {
        return $this->score;
    }
}
```

### 5. Factory Pattern

**Alasan**: Konsisten object creation, kompleks initialization

```php
class RiskFactory
{
    public static function create(CreateRiskDTO $dto): Risk
    {
        $risk = new Risk();
        $risk->title = $dto->title;
        $risk->description = $dto->description;
        $risk->inherent_risk_score = RiskScore::create(
            $dto->likelihood,
            $dto->impact
        )->getValue();
        
        return $risk;
    }
}
```

### 6. Observer Pattern (Events & Listeners)

**Alasan**: Loose coupling, side effects handling

```php
// Domain Event
class RiskCreatedEvent extends Event
{
    public function __construct(
        public Risk $risk
    ) {}
}

// Listener
class NotifyRiskOwnerListener
{
    public function handle(RiskCreatedEvent $event)
    {
        // Send notification to risk owner
    }
}
```

### 7. Strategy Pattern

**Alasan**: Multiple algorithm implementations

```php
interface ExportStrategyContract
{
    public function export(Collection $data): string;
}

class ExcelExportStrategy implements ExportStrategyContract
{
    // Excel export implementation
}

class PdfExportStrategy implements ExportStrategyContract
{
    // PDF export implementation
}
```

## Request Flow

```
HTTP Request
    │
    ▼
Middleware (Authentication, Authorization, Logging)
    │
    ▼
Route → Controller
    │
    ▼
Form Request (Validation)
    │
    ▼
Service Layer (Business Logic)
    │
    ▼
Repository (Data Access)
    │
    ▼
Eloquent Model (Database)
    │
    ▼
API Resource / View
    │
    ▼
HTTP Response
```

## Testing Strategy

```
tests/
├── Feature/
│   ├── Risk/
│   │   ├── CreateRiskTest.php
│   │   ├── UpdateRiskTest.php
│   │   ├── DeleteRiskTest.php
│   │   └── ViewRiskTest.php
│   ├── Incident/
│   └── ...
└── Unit/
    ├── Services/
    │   ├── CreateRiskServiceTest.php
    │   └── ...
    ├── ValueObjects/
    │   ├── RiskScoreTest.php
    │   └── ...
    └── Repositories/
        ├── RiskRepositoryTest.php
        └── ...
```

## Dependency Injection

Service Container configuration di `config/app.php`:

```php
'providers' => [
    // ...
    \App\Providers\RepositoryServiceProvider::class,
    \App\Providers\ServiceLayerProvider::class,
],
```

```php
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RiskRepositoryContract::class,
            RiskRepository::class
        );
        
        $this->app->bind(
            IncidentRepositoryContract::class,
            IncidentRepository::class
        );
        
        // ... more bindings
    }
}
```

## Error Handling

### Custom Exceptions

```php
namespace App\Domains\Risk\Exceptions;

class RiskNotFoundException extends \Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Risk with ID {$id} not found");
    }
}
```

### Exception Handler

```php
public function render($request, Exception $exception)
{
    if ($exception instanceof RiskNotFoundException) {
        return response()->json([
            'message' => $exception->getMessage()
        ], 404);
    }
    
    return parent::render($request, $exception);
}
```

## Performance Considerations

1. **N+1 Query Prevention**: Eager loading dengan `with()`
2. **Database Indexing**: Indexes pada frequently queried columns
3. **Caching**: Redis untuk caching KPI calculations
4. **Pagination**: Limit hasil query untuk large datasets
5. **Queuing**: Background jobs untuk exports dan notifications
6. **Database Connection Pooling**: Connection reuse

## Security Considerations

1. **Authentication**: Laravel Breeze
2. **Authorization**: Policies dan Middleware
3. **Input Validation**: Form Requests
4. **CSRF Protection**: Laravel middleware
5. **SQL Injection**: Prepared statements via Eloquent
6. **XSS Protection**: Blade escaping
7. **Rate Limiting**: Throttle middleware
8. **Audit Logging**: Track semua user actions

---

**Catatan**: Arsitektur ini dirancang untuk skalabilitas, maintainability, dan testability. Setiap layer memiliki tanggung jawab yang jelas dan terpisah.
