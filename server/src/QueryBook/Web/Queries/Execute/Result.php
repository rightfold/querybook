<?php
namespace QueryBook\Web\Queries\Execute;

final class Result {
  private $message;
  private $columns;
  private $rows;

  private function __construct(?string $message, ?array $columns, ?array $rows) {
    $this->message = $message;
    $this->columns = $columns;
    $this->rows = $rows;
  }

  public static function error(string $message): self {
    return new self($message, NULL, NULL);
  }

  public static function ok(array $columns, array $rows): self {
    return new self(NULL, $columns, $rows);
  }

  public function getMessage(): ?string {
    return $this->message;
  }

  public function getColumns(): array {
    return $this->columns ?: [];
  }

  public function getRows(): array {
    return $this->rows ?: [];
  }
}
