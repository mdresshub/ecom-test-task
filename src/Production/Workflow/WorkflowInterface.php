<?php

declare(strict_types=1);

namespace Shop\Production\Workflow;

use Shop\Production\Article;

interface WorkflowInterface extends \Iterator, \Countable
{
    public function initWorkflow(Article $article): void;

    public function removeState(string $state): void;
}
