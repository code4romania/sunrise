<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application as FoundationApplication;

trait HasViewContentFooter
{
    public function viewContentFooter(int $count, string $contentTranslate): Factory|FoundationApplication|View|Application|null
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
