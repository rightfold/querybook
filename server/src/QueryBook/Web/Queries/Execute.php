<?php
namespace QueryBook\Web\Queries;

use ErrorException;
use QueryBook\Web\Handler;
use QueryBook\Web\Queries\Execute\Result;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use stdClass;

final class Execute implements Handler {
  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  public function handle(Request $request, array $params): Response {
    $id = $params['id'];
    $query = $this->findQuery($id);
    if ($query === NULL) {
      return JsonResponse::create($id, 404);
    } else {
      $result = $this->executeQuery($query);
      $json = new stdClass();
      $json->message = $result->getMessage();
      $json->columns = $result->getColumns();
      $json->rows = $result->getRows();
      return JsonResponse::create($json);
    }
  }

  private function findQuery(string $id): ?string {
    $result = pg_query_params($this->db, "
      SELECT query
      FROM querybook.queries
      WHERE id = $1
    ", [$id]);
    $row = pg_fetch_row($result);
    if ($row === FALSE) {
      return NULL;
    } else {
      return $row[0];
    }
  }

  private function executeQuery(string $query): Result {
    try {
      $result = pg_query($this->db, $query);
    } catch (ErrorException $ex) {
      $message = $ex->getMessage();
      if (strpos($message, 'Query failed') === FALSE) {
        throw $ex;
      }
      return Result::error($message);
    }

    $columns = [];
    for ($i = 0; $i < pg_num_fields($result); ++$i) {
      $columns[] = pg_field_name($result, $i);
    }

    $rows = [];
    while (($row = pg_fetch_row($result)) !== FALSE) {
      $rows[] = $row;
    }

    return Result::ok($columns, $rows);
  }
}
