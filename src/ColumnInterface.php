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

interface ColumnInterface
{
    /**
     * @return string|null
     */
    public function renderColCell(): ?string;

    /**
     * @return string|null
     */
    public function renderHeaderCell(): ?string;

    /**
     * @return string|null
     */
    public function renderFooterCell(): ?string;

    /**
     * @param mixed $data
     * @param int $index
     * @return string|null
     */
    public function renderContentCell($data, int $index): ?string;
}
