<?php

namespace Apiato\Core\Macros\Fractal;

use App\Ship\Parents\Transformers\Transformer;
use Illuminate\Http\JsonResponse;
use Apiato\Core\Services\Response;

class Accepted {
    public function __invoke(): callable
    {
        return function (): JsonResponse {
            /** @var Response $this */
            if (is_null($this->getTransformer())) {
                $this->transformWith(Transformer::empty());
            }
            return $this->respond(202);
        };
    }
}
