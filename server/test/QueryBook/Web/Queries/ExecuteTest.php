<?php
namespace QueryBook\Web\Queries;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ExecuteTest extends TestCase {
  private $db;
  private $execute;

  public function setUp(): void {
    $this->db = pg_connect('');
    pg_query($this->db, "
      TRUNCATE TABLE querybook.queries
    ");
    $this->execute = new Execute($this->db);
  }

  public function testNoSuchQuery(): void {
    $id = 'd7501a65-d0ff-4034-b08b-cdac8bd8de17';
    list($status, $body) = $this->execute($id);
    $this->assertSame($status, 404);
  }

  public function testEmptyResult(): void {
    $id = '176c7f92-0571-4ea7-812f-9f732418f062';
    $this->insertQuery($id, "SELECT WHERE false");
    list($status, $body) = $this->execute($id);
    $this->assertSame($status, 200);
    $this->assertSame($body->message, NULL);
    $this->assertSame($body->columns, []);
    $this->assertSame($body->rows, []);
  }

  public function testNormal(): void {
    $id = '2921e270-0b64-45e6-9683-06543521f649';
    $this->insertQuery($id, "VALUES (1, 'A'), (2, 'B'), (3, 'C')");
    list($status, $body) = $this->execute($id);
    $this->assertSame($status, 200);
    $this->assertSame($body->message, NULL);
    $this->assertSame($body->columns, ['column1', 'column2']);
    $this->assertSame($body->rows, [['1', 'A'], ['2', 'B'], ['3', 'C']]);
  }

  public function testNull(): void {
    $id = '2921e270-0b64-45e6-9683-06543521f649';
    $this->insertQuery($id, "VALUES (NULL, 'A'), (2, NULL), (3, 'C')");
    list($status, $body) = $this->execute($id);
    $this->assertSame($status, 200);
    $this->assertSame($body->message, NULL);
    $this->assertSame($body->columns, ['column1', 'column2']);
    $this->assertSame($body->rows, [[NULL, 'A'], ['2', NULL], ['3', 'C']]);
  }

  private function execute(string $id) {
    $response = $this->execute->handle(new Request(), ['id' => $id]);
    return [$response->getStatusCode(), json_decode($response->getContent())];
  }

  private function insertQuery(string $id, string $query): void {
    pg_query_params($this->db, "
      INSERT INTO querybook.queries (id, query)
      VALUES ($1, $2)
    ", [$id, $query]);
  }
}
