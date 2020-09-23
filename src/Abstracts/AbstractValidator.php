<?php

namespace Sim\File\Abstracts;

use Sim\File\Interfaces\IValidator;

abstract class AbstractValidator implements IValidator
{
    /**
     * @var array $errors
     */
    protected $errors = [];

    /**
     * @var array $messages
     */
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function getErrors(): array
    {
        return array_unique($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setError(string $key, string $error)
    {
        $this->errors[$key] = $error;
        return $this;
    }

    /**
     * @param $key
     * @param $message
     * @return static
     */
    public function setMessage($key, $message)
    {
        $this->messages[$key] = $message;
        return $this;
    }

    /**
     * @param $key
     * @return string|null
     */
    protected function getMessage($key): ?string
    {
        return $this->messages[$key] ?? '';
    }
}