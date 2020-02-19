<?php

namespace Amlsoft\Dataview\Columns;

use Amlsoft\Dataview\Paginator\OffsetPaginator;
use Yiisoft\Html\Html;

class SerialColumn extends Column
{

    /**
     * @var OffsetPaginator
     */
    private OffsetPaginator $paginator;

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

        $offset = ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
        $content = $offset + $index + 1;

        return Html::tag('td', $content, $options);
    }

}
