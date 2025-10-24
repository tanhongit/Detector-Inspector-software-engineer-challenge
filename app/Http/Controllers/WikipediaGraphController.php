<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenerateGraphRequest;
use App\Services\WikipediaGraphService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WikipediaGraphController extends Controller
{
    public function __construct(
        public WikipediaGraphService $service
    ) {}

    public function index(): View
    {
        $graphs = $this->service->getAllGraphs();
        
        return view('wikipedia-graph.index', compact('graphs'));
    }

    public function generate(GenerateGraphRequest $request): RedirectResponse
    {
        try {
            $result = $this->service->generateGraph($request->validated()['url']);

            return redirect()
                ->route('wikipedia-graph.index')
                ->with('success', 'Graph generated successfully!')
                ->with('graph', $result);
        } catch (\Exception $e) {
            return redirect()
                ->route('wikipedia-graph.index')
                ->with('error', 'Error: '.$e->getMessage())
                ->withInput();
        }
    }
}
