<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Interval model
 *
 * This model is testing the QuoteIdentifier attribute, where you specify QuoteIdentifier attribute
 * to support reserved database keywords as identifier.
 */
#[Attr\Source(quoteIdentifier: true)]
#[Attr\Index(type: 'PRIMARY', fields: ['interval_id'])]
class Interval extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $interval_id;

    #[Attr\Field(type: ['type' => 'string', 'max' => 400])]
    #[Attr\QuoteIdentifier]
    protected string $interval;

    public function getIntervalId(): int
    {
        return $this->interval_id;
    }

    public function setInterval(string $interval): self
    {
        $this->interval = $interval;
        return $this;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }
}
