<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Contracts\View\View;

trait HasViewContentFooter
{
    public function viewContentFooter(int $count, string $contentTranslate): View|null
    {
        $diff = max(0, $count - $this->limit);

        if (! $diff) {
            return null;
        }

        return view('tables.footer', [
            'content' => trans_choice($contentTranslate, $diff),
            'colspan' => 2,
        ]);
    }
}
