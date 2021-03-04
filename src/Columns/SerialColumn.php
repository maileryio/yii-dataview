<?php

declare(strict_types=1);

/**
 * Dataview widget for Mailery Platform
 * @link      https://github.com/maileryio/widget-dataview
 * @package   Mailery\Widget\Dataview
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Widget\Dataview\Columns;

use Mailery\Widget\Dataview\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;

class SerialColumn extends Column
{
    /**
     * @var OffsetPaginator
     */
    private OffsetPaginator $paginator;

    /**
     * @param OffsetPaginator $paginator
     * @return $this
     */
    public function paginator(OffsetPaginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param mixed $data
     * @param string|int $index
     * @return string|null
     */
    public function renderContentCell($data, $index): ?string
    {
        if ($this->contentOptions instanceof \Closure) {
            $options = call_user_func($this->contentOptions, $data, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        $offset = ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
        $content = $offset + $index + 1;

        return (string) Html::tag('td', $content, $options)->encode(false);
    }
}
