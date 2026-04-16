# MonkeysLegion Validation

Attribute‑driven **DTO binding & validation** for the [MonkeysLegion](https://github.com/monkeyscloud) PHP 8.4 framework — self-validating constraints, property hooks, PSR-15 middleware.

---

## ✨ Features

* **Self-validating constraints** — each `#[Email]`, `#[NotBlank]`, etc. validates itself (no monolithic if/else chain)
* **PHP 8.4 property hooks** — `$result->isValid`, `$result->errors`, `$result->errorCount`
* **24 built-in constraints** — strings, numbers, dates, collections, networking, cross-field, and more
* **Automatic DTO binding** — JSON body + query parameters → strongly‑typed DTO
* **PSR‑15 middleware** — validates, returns `422 Unprocessable Entity` on failure
* **ValidationResult** — structured result object with field-level error queries
* **ValidationException** — throwable with full result for catch-and-inspect
* **Zero magic** — no doctrine proxies, only native PHP reflection & attributes
* **Extensible** — implement `ConstraintInterface` in a single class to add constraints

---

## 🛠 Requirements

| | Minimum |
|---|---|
| PHP | **8.4** |
| Extensions | `ext-json`, `ext-mbstring` |
| PSR | `psr/http-message ^2.0`, `psr/http-server-handler ^1.0`, `psr/http-server-middleware ^1.0` |

---

## 🚀 Installation

```bash
composer require monkeyscloud/monkeyslegion-validation:^2.0
```

---

## ⚡ Quick Start

### 1. Define a DTO with constraints

```php
<?php
declare(strict_types=1);

namespace App\Dto;

use MonkeysLegion\Validation\Attributes as Assert;

final readonly class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8, max: 64)]
        public string $password,

        #[Assert\SameAs(otherField: 'password')]
        public string $confirmPassword,
    ) {}
}
```

### 2. Validate directly

```php
use MonkeysLegion\Validation\Validator;

$validator = new Validator();
$result = $validator->validate($dto);

if (!$result->isValid) {
    foreach ($result->errors as $error) {
        echo "{$error->field}: {$error->message}\n";
    }
}

// Or throw on failure:
$result->throwIfInvalid();
```

### 3. Use the helper function

```php
$result = validate($dto);

if ($result->hasErrorsFor('email')) {
    echo $result->firstError('email');
}
```

### 4. PSR-15 middleware integration

```php
use MonkeysLegion\Validation\DtoBinder;
use MonkeysLegion\Validation\Validator;
use MonkeysLegion\Validation\Middleware\ValidationMiddleware;

$middleware = new ValidationMiddleware(
    binder: new DtoBinder(new Validator()),
    responseFactory: $responseFactory,   // PSR-17 ResponseFactoryInterface
    streamFactory: $streamFactory,       // PSR-17 StreamFactoryInterface
    dtoMap: [
        'user.create' => \App\Dto\CreateUserRequest::class,
    ],
);
```

When validation fails the client receives:

```json
HTTP/1.1 422 Unprocessable Entity
Content-Type: application/json

{
  "errors": [
    { "field": "email", "message": "Value must be a valid e-mail." },
    { "field": "password", "message": "Length constraint violated." }
  ]
}
```

### 5. Handler with validated DTO

```php
public function createUser(ServerRequestInterface $request): ResponseInterface
{
    /** @var CreateUserRequest $dto */
    $dto = $request->getAttribute('dto');

    $this->userService->register($dto->email, $dto->password);

    return $this->responseFactory->createResponse(201);
}
```

---

## 📦 Built-in Constraints

### Strings
| Attribute | Description |
|-----------|-------------|
| `#[NotBlank]` | Value must not be null, empty string, or empty array |
| `#[Email]` | Valid e-mail address |
| `#[Length(min, max)]` | String length within bounds (UTF-8) |
| `#[Pattern(regex)]` | Matches regular expression |
| `#[Alpha]` | Letters only |
| `#[Alnum]` | Letters and digits only |
| `#[Json]` | Valid JSON string |

### Numbers
| Attribute | Description |
|-----------|-------------|
| `#[Numeric]` | Value is numeric (`is_numeric`) |
| `#[Min(value)]` | Minimum numeric value |
| `#[Max(value)]` | Maximum numeric value |
| `#[Range(min, max)]` | Numeric range |
| `#[Decimal(scale)]` | Decimal with max scale digits |

### Collections
| Attribute | Description |
|-----------|-------------|
| `#[Count(min, max)]` | Array/iterable size within bounds |
| `#[Choice(choices)]` | Value in allowed list |

### Dates
| Attribute | Description |
|-----------|-------------|
| `#[Date(format)]` | Valid date (default: Y-m-d) |
| `#[After(otherField)]` | Date after another field |
| `#[Before(otherField)]` | Date before another field |

### Networking
| Attribute | Description |
|-----------|-------------|
| `#[Url]` | Valid URL |
| `#[Ip(allowV6)]` | Valid IP address |
| `#[UuidV4]` | Valid UUID v4 |

### Cross-field & Type
| Attribute | Description |
|-----------|-------------|
| `#[SameAs(otherField)]` | Must match another field |
| `#[Type(type)]` | PHP type check (string, int, float, bool, array, or class) |
| `#[Unique]` | Marker for uniqueness (consumer provides checker) |
| `#[Callback(fn)]` | Custom callable validation |

---

## 🪄 Creating Custom Constraints

Implement `ConstraintInterface` — the constraint is the validator:

```php
<?php
declare(strict_types=1);

namespace App\Validation;

use MonkeysLegion\Validation\Contracts\ConstraintInterface;
use MonkeysLegion\Validation\ValidationError;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final readonly class Slug implements ConstraintInterface
{
    public function __construct(
        public string $message = 'Value must be a valid URL slug.',
    ) {}

    public function validate(mixed $value, string $field, object $dto): ?ValidationError
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value)) {
            return new ValidationError($field, $this->message);
        }

        return null;
    }
}
```

Now `#[Slug]` is usable on any DTO property — no registration needed.

---

## 🧪 Testing

```bash
composer test
# or
vendor/bin/phpunit
```

57 tests, 71 assertions covering all 24 constraints.

---

## 🗺 Roadmap

* 🌐 I18n-aware validation messages via `monkeyslegion-i18n`
* 📚 Constraint composition (`#[Each(new Email())]` style)
* 🔄 Async validation for remote checks (uniqueness, etc.)
* 🧰 CLI generator for DTO scaffolding

---

## 🙌 Contributing

1. Fork & create a feature branch.
2. Follow [MonkeysLegion v2 code standards](https://github.com/MonkeysCloud/MonkeysLegion/blob/main/monkeyslegion_v2_code_standards.md).
3. Add unit tests (`vendor/bin/phpunit`).
4. Open a PR.

---

## 📄 License

Released under the MIT License © 2026 MonkeysCloud.
