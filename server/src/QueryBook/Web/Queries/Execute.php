<?php
namespace QueryBook\Web\Queries;

use QueryBook\Web\Handler;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use stdClass;

final class Execute implements Handler {
  public function handle(Request $request, array $params): Response {
    return JsonResponse::create(new stdClass());
  }
}
