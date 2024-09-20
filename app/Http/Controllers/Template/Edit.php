<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\Template;

class Edit extends Controller
{
    public function __invoke(Template $template)
    {
        return view('template.edit', [
            'template' => $template,
        ]);
    }
}
