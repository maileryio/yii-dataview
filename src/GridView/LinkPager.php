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

use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

class LinkPager extends Widget
{
    /**
     * @var array HTML attributes for the pager list tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $listOptions = [
        'class' => ['pagination'],
        'encode' => false,
    ];

    /**
     * @var array HTML attributes which will be applied to all link containers
     */
    public $linkContainerOptions = [
        'class' => ['page-item'],
        'encode' => false,
    ];

    /**
     * @var array HTML attributes for the link in a pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $linkOptions = [
        'class' => ['page-link'],
        'encode' => false,
    ];

    /**
     * @var string the CSS class for the each page button.
     */
    public $pageCssClass;

    /**
     * @var string the CSS class for the "first" page button.
     */
    public $firstPageCssClass = 'first';

    /**
     * @var string the CSS class for the "last" page button.
     */
    public $lastPageCssClass = 'last';

    /**
     * @var string the CSS class for the "previous" page button.
     */
    public $prevPageCssClass = 'prev';

    /**
     * @var string the CSS class for the "next" page button.
     */
    public $nextPageCssClass = 'next';

    /**
     * @var string the CSS class for the active (currently selected) page button.
     */
    public $activePageCssClass = 'active';

    /**
     * @var string the CSS class for the disabled page buttons.
     */
    public $disabledPageCssClass = 'disabled';

    /**
     * @var array the options for the disabled tag to be generated inside the disabled list element.
     * In order to customize the html tag, please use the tag key.
     *
     * ```php
     * $disabledListItemSubTagOptions = ['class' => 'disabled-link'];
     * ```
     */
    public $disabledListItemSubTagOptions = [];

    /**
     * @var int maximum number of page buttons that can be displayed. Defaults to 10.
     */
    public $maxButtonCount = 10;

    /**
     * @var bool|string the text label for the "first" page button. Note that this will NOT be HTML-encoded.
     * If it's specified as true, page number will be used as label.
     * Default is false that means the "first" page button will not be displayed.
     */
    public $firstPageLabel = false;

    /**
     * @var bool|string the text label for the "last" page button. Note that this will NOT be HTML-encoded.
     * If it's specified as true, page number will be used as label.
     * Default is false that means the "last" page button will not be displayed.
     */
    public $lastPageLabel = false;

    /**
     * @var bool whether to register link tags in the HTML header for prev, next, first and last page.
     * Defaults to `false` to avoid conflicts when multiple pagers are used on one page.
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     * @see registerLinkTags()
     */
    public $registerLinkTags = false;

    /**
     * @var bool Hide widget when only one page exist.
     */
    public $hideOnSinglePage = true;

    /**
     * @var bool whether to render current page button as disabled.
     */
    public $disableCurrentPageButton = false;

    /**
     * @var Pagination the paginator object that this pager is associated with.
     * You must set this property in order to make LinkPager work.
     */
    private OffsetPaginator $paginator;

    /**
     * @var array HTML attributes for the pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    private array $options = [
        'encode' => false,
    ];

    /**
     * @var type
     */
    private \Closure $urlGenerator;

    /**
     * @var bool|string the label for the "next" page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "next" page button will not be displayed.
     */
    private $nextPageLabel = "<span aria-hidden=\"true\">&raquo;</span>\n<span class=\"sr-only\">Next</span>";

    /**
     * @var bool|string the text label for the "previous" page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "previous" page button will not be displayed.
     */
    private $prevPageLabel = "<span aria-hidden=\"true\">&laquo;</span>\n<span class=\"sr-only\">Previous</span>";

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
     * @param OffsetPaginator $paginator
     * @return $this
     */
    public function paginator(OffsetPaginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param \Closure $urlGenerator
     * @return $this
     */
    public function urlGenerator(\Closure $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    /**
     * @param string $nextPageLabel
     * @return $this
     */
    public function nextPageLabel(string $nextPageLabel)
    {
        $this->nextPageLabel = $nextPageLabel;

        return $this;
    }

    /**
     * @param string $prevPageLabel
     * @return $this
     */
    public function prevPageLabel(string $prevPageLabel)
    {
        $this->prevPageLabel = $prevPageLabel;

        return $this;
    }

    /**
     * Initializes the pager.
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->paginator === null) {
            throw new InvalidConfigException('The "paginator" property must be set.');
        }
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run(): string
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'nav');
        $html = Html::beginTag($tag, $options);
        $html .= $this->renderPageButtons();
        $html .= Html::endTag($tag);

        return $html;
    }

    /**
     * Registers relational link tags in the html header for prev, next, first and last page.
     * These links are generated using [[\yii\data\Pagination::getLinks()]].
     * @see http://www.w3.org/TR/html401/struct/links.html#h-12.1.2
     */
    protected function registerLinkTags()
    {
        $view = $this->getView();
        foreach ($this->paginator->getLinks() as $rel => $href) {
            $view->registerLinkTag(['rel' => $rel, 'href' => $href], $rel);
        }
    }

    /**
     * Renders the page buttons.
     * @return string the rendering result
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->paginator->getTotalPages();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->paginator->getCurrentPage() - 1;

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton(
                    $firstPageLabel,
                    0,
                    $this->firstPageCssClass,
                    $currentPage <= 0,
                    false
            );
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton(
                    $this->prevPageLabel,
                    $page,
                    $this->prevPageCssClass,
                    $currentPage <= 0,
                    false
            );
        }

        // internal pages
        [$beginPage, $endPage] = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton(
                    $i + 1,
                    $i,
                    null,
                    $this->disableCurrentPageButton && $i == $currentPage,
                    $i == $currentPage
            );
        }

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton(
                    $this->nextPageLabel,
                    $page,
                    $this->nextPageCssClass,
                    $currentPage >= $pageCount - 1,
                    false
            );
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton(
                    $lastPageLabel,
                    $pageCount - 1,
                    $this->lastPageCssClass,
                    $currentPage >= $pageCount - 1,
                    false
            );
        }

        $options = $this->listOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');

        return Html::tag($tag, implode("\n", $buttons), $options);
    }

    /**
     * Renders a page button.
     * You may override this method to customize the generation of page buttons.
     * @param string $label the text label for the button
     * @param int $page the page number
     * @param string $class the CSS class for the page button.
     * @param bool $disabled whether this page button is disabled
     * @param bool $active whether this page button is active
     * @return string the rendering result
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $linkOptions = ArrayHelper::merge($linkOptions, $disabledItemOptions);
            $linkOptions['tabindex'] = '-1';
        }

        $url = call_user_func($this->urlGenerator, ++$page);

        return Html::tag($linkWrapTag, Html::a((string) $label, $url, $linkOptions), $options);
    }

    /**
     * @return array the begin and end pages that need to be displayed.
     */
    protected function getPageRange()
    {
        $currentPage = $this->paginator->getCurrentPage();
        $pageCount = $this->paginator->getTotalPages();

        $beginPage = max(0, $currentPage - (int) ($this->maxButtonCount / 2));
        if (($endPage = $beginPage + $this->maxButtonCount - 1) >= $pageCount) {
            $endPage = $pageCount - 1;
            $beginPage = max(0, $endPage - $this->maxButtonCount + 1);
        }

        return [$beginPage, $endPage];
    }
}
