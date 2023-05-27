<?php

/**
 * This file is part of the guanguans/laravel-soar.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Guanguans\LaravelSoar\Outputs;

use Illuminate\Support\Collection;

class ConsoleOutput extends Output
{
    public function output(Collection $scores, $dispatcher)
    {
        if (! $this->isHtmlResponse($dispatcher)) {
            return;
        }

        $js = $this->transformToJs($scores);
        $content = $dispatcher->getContent();

        // Try to put the widget at the end, directly before the </body>
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos).$js.substr($content, $pos);
        } else {
            $content = $content.$js;
        }

        // Update the new content and reset the content length
        $dispatcher->setContent($content);
        $dispatcher->headers->remove('Content-Length');
    }

    protected function transformToJs(Collection $scores): string
    {
        return $scores->pipe(function ($scores) {
            $js = $scores->reduce(function ($js, $score) {
                unset($score['Basic']);
                $score = str_replace('`', '\`', to_pretty_json($score));

                return $js.<<<JS
console.warn(`
$score
`);
JS;
            }, '');

            return <<<JS
<script type="text/javascript">$js</script>
JS;
        });
    }
}
