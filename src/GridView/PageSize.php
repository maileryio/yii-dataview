<?php

declare(strict_types=1);

/**
 * Dataview widget for Mailery Platform
 * @link      https://github.com/maileryio/widget-dataview
 * @package   Mailery\Widget\Dataview
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2020, Mailery (https://mailery.io/)
 */

namespace Mailery\Widget\Dataview\GridView;

use FormManager\Factory as F;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\Widget;

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
    private array $options = [
        'encode' => false,
    ];

    /**
     * @var array
     */
    private array $inputOptions = [
        'encode' => false,
    ];

    /**
     * @var type @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $caption
     * @return $this
     */
    public function caption(string $caption)
    {
        $this->caption = ArrayHelper::merge(
            $this->options,
            $options
        );

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
        $this->inputOptions = ArrayHelper::merge(
            $this->inputOptions,
            $inputOptions
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        $inputOptions = $this->inputOptions;
        if (!isset($inputOptions['name'])) {
            $inputOptions['name'] = 'pageSize';
        }

        $input = F::select(null, $this->sizes, $inputOptions);

        if (($content = $this->caption) === null) {
            $content = $this->translator->translate('Show {input} entries', [], 'dataview');
        }

        return Html::tag($tag, strtr($content, ['{input}' => $input]), $options);
    }
}
