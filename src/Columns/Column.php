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

use Mailery\Widget\Dataview\ColumnInterface;
use Yiisoft\Html\Html;

abstract class Column implements ColumnInterface
{
    /**
     * @var string
     */
    protected string $header = '';

    /**
     * @var string
     */
    protected string $footer = '';

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @var array
     */
    protected array $headerOptions = [
        'encode' => false,
    ];

    /**
     * @var array
     */
    protected array $footerOptions = [
        'encode' => false,
    ];

    /**
     * @var array|\Closure
     */
    protected array $contentOptions = [
        'encode' => false,
    ];

    /**
     * @var string
     */
    protected string $emptyText = '-';

    /**
     * @param string $header
     * @return $this
     */
    public function header(string $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param string $footer
     * @return $this
     */
    public function footer(string $footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = ArrayHelper::merge(
            $this->options,
            $options
        );

        return $this;
    }

    /**
     * @param array $headerOptions
     * @return $this
     */
    public function headerOptions(array $headerOptions)
    {
        $this->headerOptions = ArrayHelper::merge(
            $this->headerOptions,
            $headerOptions
        );

        return $this;
    }

    /**
     * @param array $footerOptions
     * @return $this
     */
    public function footerOptions(array $footerOptions)
    {
        $this->footerOptions = ArrayHelper::merge(
            $this->footerOptions,
            $footerOptions
        );

        return $this;
    }

    /**
     * @param array|\Closure $contentOptions
     * @return $this
     */
    public function contentOptions($contentOptions)
    {
        $this->contentOptions = ArrayHelper::merge(
            $this->contentOptions,
            $contentOptions
        );

        return $this;
    }

    /**
     * @param bool|string $emptyText
     * @return $this
     */
    public function emptyText($emptyText)
    {
        $this->emptyText = $emptyText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function renderColCell(): ?string
    {
        if (empty($this->options)) {
            return null;
        }

        return Html::tag('col', '', $this->options);
    }

    /**
     * @return string|null
     */
    public function renderHeaderCell(): ?string
    {
        return Html::tag('th', $this->header, $this->headerOptions);
    }

    /**
     * @return string|null
     */
    public function renderFooterCell(): ?string
    {
        return Html::tag('td', $this->footer, $this->footerOptions);
    }
}
