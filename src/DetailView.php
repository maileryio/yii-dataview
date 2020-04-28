<?php

declare(strict_types=1);

/**
 * Dataview widget for Mailery Platform
 * @link      https://github.com/maileryio/widget-dataview
 * @package   Mailery\Widget\Dataview
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Widget\Dataview;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

class DetailView extends Widget
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private array $options = [
        'class' => 'table table-striped table-bordered detail-view',
    ];

    /**
     * @var array
     */
    private array $attributes;

    /**
     * @var string
     */
    private string $template = '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';

    /**
     * @var false|string
     */
    private $emptyText = '';

    /**
     * @var array
     */
    private array $emptyTextOptions = [];

    /**
     * @param mixed $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function attributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function template(string $template)
    {
        $this->template = $template;

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
     * @param array $emptyTextOptions
     * @return $this
     */
    public function emptyTextOptions(array $emptyTextOptions)
    {
        $this->emptyTextOptions = $emptyTextOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');

        return Html::tag($tag, implode("\n", $rows), $options);
    }

    /**
     * @param array $attribute
     * @param int $index
     * @return string
     */
    private function renderAttribute(array $attribute, int $index): string
    {
        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', []));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', []));

            if (is_callable($attribute['value'])) {
                $attribute['value'] = call_user_func($attribute['value'], $this->data, $index);
            }

            if (empty($attribute['value'])) {
                $attribute['value'] = $this->renderEmpty();
            }

            return strtr(
                $this->template,
                [
                    '{label}' => $attribute['label'],
                    '{value}' => $attribute['value'],
                    '{captionOptions}' => $captionOptions,
                    '{contentOptions}' => $contentOptions,
                ]
            );
        }

        return call_user_func($this->template, $attribute, $index);
    }

    /**
     * @return string
     */
    private function renderEmpty(): string
    {
        if ($this->emptyText === false) {
            return '';
        }

        $options = $this->emptyTextOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $this->emptyText, $options);
    }
}
