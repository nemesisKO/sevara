<?php

namespace App\Http\Controllers;

use App\Models\PackagingFormat;
use App\Models\PrivateLabelInquiry;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contact', [
            'formats' => PackagingFormat::orderBy('order')->orderBy('id')->get(),
            'markets' => PrivateLabelInquiry::MARKETS,
        ]);
    }
}
