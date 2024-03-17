<?php

namespace Blrf\Bookstore;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Blrf\Orm\Factory;
use Blrf\Orm\Model;
use Blrf\Orm\Model\Meta;
use Blrf\Orm\Model\QueryBuilder;
use Blrf\Orm\Model\Result;
use Blrf\Orm\Model\Exception\NotFoundException;
use React\Promise\PromiseInterface;

use function React\Async\await;

abstract class ModelController
{
    abstract protected function getModel(): string;

    /**
     * Default handler
     *
     */
    public function __invoke(ServerRequestInterface $req)
    {
        /**
         * Get model metadata
         */
        if ($req->getMethod() === 'GET' && $req->getAttribute('opt') == 'metadata') {
            return Factory::getModelManager()->getMeta($this->getModel())->then(
                function (Meta $meta) {
                    return Response::json($meta->getData());
                }
            );
        }
        /**
         * Get model by Id
         */
        if ($req->getMethod() === 'GET' && $req->getAttribute('id') > 0) {
            $modelClass = $this->getModel();
            return $modelClass::findByPk($req->getAttribute('id'))->then(
                function (Model $model) use ($req) {
                    /**
                     * NOTE: We cannot return Response::json($model), as that would call
                     *       jsonSerialize() method on model, which uses await() and we get a strange
                     *       error.
                     *
                     * Call model toArray() which will convert an object to array.
                     *
                     * If related was provided, orm will resolve related models
                     *
                     * This feature is questionable as we go one level deep, some might
                     * want to go deeper, but we may cause loops, etc.
                     */
                    return $model->toArray($req->getAttribute('opt') == 'related');
                }
            )->then(
                function (array $data) {
                    return Response::json($data);
                }
            )->otherwise(
                function (NotFoundException $e) {
                    return Response::json(['error' => 'Model not found']);
                }
            );
        }
        /**
         * Create model
         */
        if ($req->getMethod() === 'PUT') {
            $data = json_decode((string) $req->getBody(), true);
            if (!empty($data)) {
                $modelClass = $this->getModel();
                $model = new $modelClass();
                return $model->assign($data)->then(
                    function (Model $model) {
                        return $model->insert();
                    }
                )->then(
                    function (Model $model) {
                        return $model->toArray();
                    }
                )->then(
                    function (array $data) {
                        return Response::json($data);
                    }
                );
            }
        }
        /**
         * Delete model by Id
         */
        if ($req->getMethod() === 'DELETE' && $req->getAttribute('id') > 0) {
            $modelClass = $this->getModel();
            return $modelClass::findByPk($req->getAttribute('id'))->then(
                function (Model $model) {
                    return $model->delete();
                }
            )->then(
                function (bool $deleted) {
                    return Response::json([
                        'deleted'   => $deleted
                    ]);
                }
            );
        }

        /**
         * Search models
         */
        if ($req->getMethod() === 'POST') {
            $data = json_decode((string) $req->getBody(), true);
            $modelClass = $this->getModel();
            if ($req->getAttribute('opt') === 'stream') {
                /**
                 * Return JSON stream of models
                 */
                return $modelClass::find($data)->then(
                    function (QueryBuilder $qb): Response {
                        /**
                         * Streaming response.
                         */
                        return new Response(
                            200,
                            [
                                'Content-Type' => 'application/x-ndjson'
                            ],
                            new ModelResultStreamNdJson($qb->stream())
                        );
                    }
                );
            } else {
                return $modelClass::find($data)->then(
                    function (QueryBuilder $qb): PromiseInterface {
                        return $qb->execute();
                    }
                )->then(
                    function (Result $res) {
                        $data = [];
                        /**
                         * Result could probably be some kind of streaming response
                         * as we convert rows to json?
                         */
                        foreach ($res as $model) {
                            $data[] = await($model->toArray());
                        }
                        return Response::json($data);
                    }
                );
            }
        }
        return Response::plaintext(
            "METHOD: " . $req->getMethod() . "\n " .
            print_r($req, true)
        );
    }
}
