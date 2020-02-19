<?php

namespace Mailery\Dataview\GridView;

use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;
use Yiisoft\Arrays\ArrayHelper;
use FormManager\Factory as F;

/**
 * ```php
 * GridView\PageSize::widget()
 *     ->inputOptions([
 *         'class' => 'form-control',
 *         'style' => [
 *             'width: auto;',
 *             'display: inline-block;',
 *             'background-color: transparent;',
 *         ],
 *     ]);
 * ```
 */
class PageSize extends Widget
{

    /**
     * @var string|null
     */
    private ?string $caption;

    /**
     * @var array
     */
    private array $sizes = [
        10 => 10,
        15 => 15,
        20 => 20,
    ];

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var array
     */
    private array $inputOptions = [];

    /**
     * @param string $caption
     * @return $this
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param array $sizes
     * @return $this
     */
    public function sizes(array $sizes)
    {
        $this->sizes = $sizes;
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
     * @param array $inputOptions
     * @return $this
     */
    public function inputOptions(array $inputOptions)
    {
        $this->inputOptions = $inputOptions;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        $inputOptions = $this->inputOptions;
        if (!isset($inputOptions['name'])) {
            $inputOptions['name'] = 'pageSize';
        }

        $input = F::select(null, $this->sizes, $inputOptions);

        if (($content = $this->caption) === null) {
            $content = __('Show {input} entries', 'dataview');
        }

        return Html::tag($tag, strtr($content, ['{input}' => $input]), $options);
    }

}