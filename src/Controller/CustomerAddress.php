<?php

namespace Blrf\Bookstore\Controller;

use Blrf\Bookstore\ModelController;
use Blrf\Bookstore\Model\CustomerAddress as CustomerAddressModel;
use Blrf\Dbal\Query\Condition;
use Blrf\Orm\Model;
use Blrf\Orm\Model\QueryBuilder;
use Blrf\Orm\Model\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use React\Http\Message\Response;

use function React\Async\await;

class CustomerAddress extends ModelController
{
    protected function getModel(): string
    {
        return CustomerAddressModel::class;
    }

    /**
     * Special handling of customer address
     *
     */
    public function __invoke(ServerRequestInterface $req)
    {
        $attrs = $req->getAttributes();
        if ($req->getMethod() === 'GET' && isset($attrs['cid']) && isset($attrs['aid'])) {
            $modelClass = $this->getModel();
            return $modelClass::findByPk($attrs['cid'], $attrs['aid'])->then(
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
            );
        }
        if ($req->getMethod() === 'POST' && isset($attrs['cid'])) {
            $data = json_decode((string) $req->getBody(), true);
            $modelClass = $this->getModel();
            return $modelClass::find($data)->then(
                function (QueryBuilder $qb) use ($attrs): PromiseInterface {
                    $qb->andWhere(new Condition('customer_id'));
                    $qb->addParameter($attrs['cid']);
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
        return parent::__invoke($req);
    }
}
