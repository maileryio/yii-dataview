<?php

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
