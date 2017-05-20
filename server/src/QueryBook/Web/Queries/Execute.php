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
      return JsonResponse::create($query);
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
}
