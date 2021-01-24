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
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

class GridView extends Widget
{
    /**
     * @var bool
     */
    public bool $showHeader = true;

    /**
     * @var bool
     */
    public bool $showFooter = false;

    /**
     * @var bool
     */
    public bool $placeFooterAfterBody = false;

    /**
     * @var ColumnInterface[]
     */
    private array $columns = [];

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var array
     */
    private array $headerRowOptions = [];

    /**
     * @var array
     */
    private array $footerRowOptions = [];

    /**
     * @var bool
     */
    private bool $showOnEmpty = false;

    /**
     * @var false|string
     */
    private $emptyText = '';

    /**
     * @var array
     */
    private array $emptyTextOptions = [];

    /**
     * @var string
     */
    private string $caption = '';

    /**
     * @var array
     */
    private array $captionOptions = [];

    /**
     * @var array
     */
    private array $tableOptions = [];

    /**
     * @var array
     */
    private array $headOptions = [];

    /**
     * @var array|\Closure
     */
    private $rowOptions = [];

    /**
     * @var \Closure
     */
    private $beforeRow;

    /**
     * @var \Closure
     */
    private $afterRow;

    /**
     * @param ColumnInterface[] $columns
     */
    public function columns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param PaginatorInterface $paginator
     * @return $this
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;

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
     * @param array $headerRowOptions
     * @return $this
     */
    public function headerRowOptions(array $headerRowOptions)
    {
        $this->headerRowOptions = $headerRowOptions;

        return $this;
    }

    /**
     * @param array $footerRowOptions
     * @return $this
     */
    public function footerRowOptions(array $footerRowOptions)
    {
        $this->footerRowOptions = $footerRowOptions;

        return $this;
    }

    /**
     * @param bool $showOnEmpty
     * @return $this
     */
    public function showOnEmpty(bool $showOnEmpty)
    {
        $this->showOnEmpty = $showOnEmpty;

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
     * @param string $caption
     * @return $this
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @param array $captionOptions
     * @return $this
     */
    public function captionOptions(array $captionOptions)
    {
        $this->captionOptions = $captionOptions;

        return $this;
    }

    /**
     * @param array $tableOptions
     * @return $this
     */
    public function tableOptions(array $tableOptions)
    {
        $this->tableOptions = $tableOptions;

        return $this;
    }

    /**
     * @param array $headOptions
     * @return $this
     */
    public function headOptions(array $headOptions)
    {
        $this->headOptions = $headOptions;

        return $this;
    }

    /**
     * @param array|\Closure $rowOptions
     * @return $this
     */
    public function rowOptions($rowOptions)
    {
        $this->rowOptions = $rowOptions;

        return $this;
    }

    /**
     * @param \Closure $beforeRow
     * @return $this
     */
    public function beforeRow(\Closure $beforeRow)
    {
        $this->beforeRow = $beforeRow;

        return $this;
    }

    /**
     * @param \Closure $afterRow
     * @return $this
     */
    public function afterRow(\Closure $afterRow)
    {
        $this->afterRow = $afterRow;

        return $this;
    }

    /**
     * @param bool $showHeader
     * @return $this
     */
    public function showHeader(bool $showHeader)
    {
        $this->showHeader = $showHeader;

        return $this;
    }

    /**
     * @param bool $showFooter
     * @return $this
     */
    public function showFooter(bool $showFooter)
    {
        $this->showFooter = $showFooter;

        return $this;
    }

    /**
     * @param bool $placeFooterAfterBody
     * @return $this
     */
    public function placeFooterAfterBody(bool $placeFooterAfterBody)
    {
        $this->placeFooterAfterBody = $placeFooterAfterBody;

        return $this;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $this->renderItems(), $options);
    }

    /**
     * @return string
     */
    public function renderItems(): string
    {
        $caption = $this->renderCaption();
        $columnGroup = $this->renderColumnGroup();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();
        $tableFooter = false;
        $tableFooterAfterBody = false;

        if ($this->showFooter) {
            if ($this->placeFooterAfterBody) {
                $tableFooterAfterBody = $this->renderTableFooter();
            } else {
                $tableFooter = $this->renderTableFooter();
            }
        }

        $content = array_filter([
            $caption,
            $columnGroup,
            $tableHeader,
            $tableFooter,
            $tableBody,
            $tableFooterAfterBody,
        ]);

        return Html::tag('table', implode("\n", $content), $this->tableOptions);
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

    /**
     * @return string|null
     */
    private function renderCaption(): ?string
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions);
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function renderColumnGroup(): ?string
    {
        $cols = [];
        foreach ($this->columns as $column) {
            /** @var $column ColumnInterface */
            $cols[] = $column->renderColCell();
        }

        if (!empty(array_filter($cols))) {
            return Html::tag('colgroup', implode("\n", $cols));
        }

        return null;
    }

    /**
     * @return string|null
     */
    private function renderTableHeader(): string
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column ColumnInterface */
            $cells[] = $column->renderHeaderCell();
        }

        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
//        if ($this->filterPosition === self::FILTER_POS_HEADER) {
//            $content = $this->renderFilters() . $content;
//        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
//            $content .= $this->renderFilters();
//        }

        return Html::tag('thead', "\n$content\n", $this->headOptions);
    }

    /**
     * @return string
     */
    private function renderTableFooter(): string
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column ColumnInterface */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
//        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
//            $content .= $this->renderFilters();
//        }
        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * @return string
     */
    private function renderTableBody()
    {
        $rows = [];
        foreach ($this->paginator->read() as $index => $data) {
            if ($this->beforeRow !== null) {
                $row = call_user_func($this->beforeRow, $data, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }

            $rows[] = $this->renderTableRow($data, $index);

            if ($this->afterRow !== null) {
                $row = call_user_func($this->afterRow, $data, $index, $this);
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }
        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }

        return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
    }

    /**
     * @param mixed $data
     * @param int $index
     * @return string
     */
    private function renderTableRow($data, int $index)
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column ColumnInterface */
            $cells[] = $column->renderContentCell($data, $index);
        }

        if ($this->rowOptions instanceof \Closure) {
            $options = call_user_func($this->rowOptions, $data, $index, $this);
        } else {
            $options = $this->rowOptions;
        }

        if (!isset($options['data-key'])) {
            $options['data-key'] = ++$index;
        }

        return Html::tag('tr', implode('', $cells), $options);
    }
}
