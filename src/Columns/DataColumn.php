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

use Yiisoft\Html\Html;

class DataColumn extends Column
{
    /**
     * @var \Closure|string
     */
    private $content;

    /**
     * @param \Closure|string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

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

        if ($this->content instanceof \Closure) {
            $content = call_user_func($this->content, $data, $index, $this);
        } else {
            $content = $this->content;
        }

        if (empty($content)) {
            $content = $this->emptyText;
        }

        return (string) Html::tag('td', (string) $content, $options)->encode(false);
    }
}
