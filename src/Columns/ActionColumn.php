<?php

namespace Mailery\Dataview\Columns;

use Yiisoft\Html\Html;

class ActionColumn extends Column
{

    /**
     * @var string|\Closure
     */
    protected $view;

    /**
     * @var string|\Closure
     */
    protected $update;

    /**
     * @var string|\Closure
     */
    protected $delete;

    /**
     * @var string
     */
    protected string $layout = '{view} {update} {delete}';

    /**
     * @param string|\Closure $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @param string|\Closure $update
     * @return $this
     */
    public function update($update)
    {
        $this->update = $update;
        return $this;
    }

    /**
     * @param string|\Closure $delete
     * @return $this
     */
    public function delete($delete)
    {
        $this->delete = $delete;
        return $this;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function layout(string $layout)
    {
        $this->layout = $layout;
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

        $content = preg_replace_callback(
            '/{\\w+}/',
            function ($matches) use($data, $index) {
                $content = $this->renderSection($matches[0], $data, $index);

                if ($content instanceof \Closure) {
                    $content = call_user_func($content, $data, $index, $this);
                }

                return $content === null ? $matches[0] : $content;
            },
            $this->layout
        );

        if (empty($content)) {
            $content = $this->emptyText;
        }

        return Html::tag('td', $content, $options);
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param int $index
     * @return string|null
     */
    public function renderSection(string $name, $data, int $index): ?string
    {
        $fnMapContent = function (string $name) {
            switch ($name) {
                case '{view}':
                    return $this->view;
                case '{update}':
                    return $this->update;
                case '{delete}':
                    return $this->delete;
                default:
                    return null;
            }
        };

        $content = $fnMapContent($name);

        if ($content instanceof \Closure) {
            $content = call_user_func($content, $data, $index, $this);
        }
        return $content;
    }

}
