<?php

namespace Mailery\Widget\Dataview\Columns;

use Yiisoft\Html\Html;

class DataColumn extends Column
{

    /**
     * @var string|\Closure
     */
    private $content;

    /**
     * @param string|\Closure $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;
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

        if ($this->content instanceof \Closure) {
            $content = call_user_func($this->content, $data, $index, $this);
        } else {
            $content = $this->content;
        }

        if (empty($content)) {
            $content = $this->emptyText;
        }

        return Html::tag('td', $content, $options);
    }

}
