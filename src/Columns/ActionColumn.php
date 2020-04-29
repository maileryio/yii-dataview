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

class ActionColumn extends Column
{
    /**
     * @var \Closure|string
     */
    protected $view;

    /**
     * @var \Closure|string
     */
    protected $update;

    /**
     * @var \Closure|string
     */
    protected $delete;

    /**
     * @var string
     */
    protected string $layout = '{view} {update} {delete}';

    /**
     * @param \Closure|string $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param \Closure|string $update
     * @return $this
     */
    public function update($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * @param \Closure|string $delete
     * @return $this
     */
    public function delete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function layout(string $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @param mixed $data
     * @param int $index
     * @return string|null
     */
    public function renderContentCell($data, int $index): ?string
    {
        if ($this->contentOptions instanceof \Closure) {
            $options = call_user_func($this->contentOptions, $data, $index, $this);
        } else {
            $options = $this->contentOptions;
        }

        $content = preg_replace_callback(
            '/{\\w+}/',
            function ($matches) use ($data, $index) {
                $content = $this->renderSection($matches[0], $data, $index);

                if ($content instanceof \Closure) {
                    $content = call_user_func($content, $data, $index, $this);
                }

                return $content === null ? $matches[0] : $content;
            },
            $this->layout
        );

        if (empty($content)) {
            $content = $this->emptyText;
        }

        return Html::tag('td', $content, $options);
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $index
     * @return string|null
     */
    public function renderSection(string $name, $data, int $index): ?string
    {
        $fnMapContent = function (string $name) {
            switch ($name) {
                case '{view}':
                    return $this->view;
                case '{update}':
                    return $this->update;
                case '{delete}':
                    return $this->delete;
                default:
                    return null;
            }
        };

        $content = $fnMapContent($name);

        if ($content instanceof \Closure) {
            $content = call_user_func($content, $data, $index, $this);
        }

        return (string) $content;
    }
}
