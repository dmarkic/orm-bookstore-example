<?php

declare(strict_types=1);

namespace Blrf\Bookstore\Model;

use Blrf\Orm\Model\Attribute as Attr;
use Blrf\Orm\Model;

/**
 * Example model that is described with attributes
 *
 * Model attribute or Source should be specified to enable Attribute meta data driver.
 */
#[Attr\Model]
#[Attr\Source]
#[Attr\Index(type: 'PRIMARY', fields: ['language_id'])]
class BookLanguage extends Model
{
    #[Attr\Field]
    #[Attr\GeneratedValue]
    protected int $language_id;
    #[Attr\Field(type: ['type' => 'string', 'min' => 0, 'max' => 8])]
    protected string $language_code;
    #[Attr\Field(type: ['type' => 'string', 'max' => 50])]
    protected string $language_name;

    public function getLanguageId(): int
    {
        return $this->language_id;
    }

    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    public function getLanguageName(): string
    {
        return $this->language_name;
    }
}
