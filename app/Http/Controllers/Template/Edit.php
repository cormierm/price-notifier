<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Inertia\Inertia;

class Edit extends Controller
{
    public function __invoke(Template $template)
    {
        return Inertia::render('Template/Form', [
            'type' => 'Update',
            'template' => $template,
        ]);
    }
}
