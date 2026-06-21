<?php
/**
 * TravelMate - Input Validator
 *
 * Reusable validation rules for all form submissions.
 * Returns structured errors array.
 */

class Validator
{
    private array $errors = [];
    private array $data   = [];

    /**
     * Create a new validator instance for a dataset.
     *
     * @param array $data  e.g., $_POST
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // --------------------------------------------------------
    // Rule Definitions
    // --------------------------------------------------------

    /**
     * Field is required (non-empty after trim).
     */
    public function required(string $field, string $label): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value === '') {
            $this->errors[$field][] = "{$label} is required.";
        }
        return $this;
    }

    /**
     * Field must be a valid email format.
     */
    public function email(string $field, string $label = 'Email'): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    /**
     * Field must have a minimum string length.
     */
    public function minLength(string $field, int $min, string $label): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field][] = "{$label} must be at least {$min} characters.";
        }
        return $this;
    }

    /**
     * Field must have a maximum string length.
     */
    public function maxLength(string $field, int $max, string $label): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value !== '' && mb_strlen($value) > $max) {
            $this->errors[$field][] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    /**
     * Field must match another field (password confirmation).
     */
    public function matches(string $field, string $otherField, string $label): self
    {
        $value      = $this->data[$field]      ?? '';
        $otherValue = $this->data[$otherField] ?? '';
        if ($value !== $otherValue) {
            $this->errors[$field][] = "{$label} does not match.";
        }
        return $this;
    }

    /**
     * Field must be a valid date (Y-m-d).
     */
    public function date(string $field, string $label): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                $this->errors[$field][] = "{$label} must be a valid date (YYYY-MM-DD).";
            }
        }
        return $this;
    }

    /**
     * Field must be a numeric value greater than $min.
     */
    public function min(string $field, float $min, string $label): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && (float)$value < $min) {
            $this->errors[$field][] = "{$label} must be at least {$min}.";
        }
        return $this;
    }

    /**
     * Second date field must be >= first date field.
     */
    public function dateAfter(string $field, string $afterField, string $label, string $afterLabel): self
    {
        $d1 = $this->data[$afterField] ?? '';
        $d2 = $this->data[$field]      ?? '';
        if ($d1 !== '' && $d2 !== '' && $d2 < $d1) {
            $this->errors[$field][] = "{$label} must be on or after {$afterLabel}.";
        }
        return $this;
    }

    /**
     * Field must be alphanumeric + underscores only.
     */
    public function alphanumeric(string $field, string $label): self
    {
        $value = trim($this->data[$field] ?? '');
        if ($value !== '' && !preg_match('/^[a-zA-Z0-9_]+$/', $value)) {
            $this->errors[$field][] = "{$label} may only contain letters, numbers, and underscores.";
        }
        return $this;
    }

    /**
     * Field must be one of a list of allowed values.
     */
    public function in(string $field, array $allowed, string $label): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field][] = "{$label} has an invalid value.";
        }
        return $this;
    }

    // --------------------------------------------------------
    // Results
    // --------------------------------------------------------

    /**
     * Returns true if there are no validation errors.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Returns true if validation failed.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get all errors.
     *
     * @return array ['field' => ['error message', ...]]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error message for a specific field.
     */
    public function firstError(string $field): string
    {
        return $this->errors[$field][0] ?? '';
    }

    /**
     * Get the validated (trimmed) value of a field.
     */
    public function getValue(string $field): string
    {
        return trim($this->data[$field] ?? '');
    }
}
