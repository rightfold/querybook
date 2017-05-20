<?php
namespace QueryBook\Web\Queries;

use QueryBook\Web\Handler;
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
      $result = new stdClass();
      list($result->columns, $result->rows) =
        $this->executeQuery($query);
      return JsonResponse::create($result);
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

  private function executeQuery(string $query): array {
    $result = pg_query($this->db, $query);

    $columns = array_map(function($i) use($result) {
      return pg_field_name($result, $i);
    }, range(0, pg_num_fields($result) - 1));

    $rows = [];
    while (($row = pg_fetch_row($result)) !== FALSE) {
      $rows[] = $row;
    }

    return [$columns, $rows];
  }
}
