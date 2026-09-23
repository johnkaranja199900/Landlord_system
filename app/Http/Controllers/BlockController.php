<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Block;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:blocks.view')->only(['index', 'show']);
        $this->middleware('permission:blocks.create')->only(['create', 'store']);
        $this->middleware('permission:blocks.update')->only(['edit', 'update']);
        $this->middleware('permission:blocks.delete')->only('destroy');
    }

    public function index(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('blocks.index', [
            'property' => $property,
            'blocks' => $property->blocks()->withCount('units')->get(),
        ]);
    }

    public function show(Block $block): View
    {
        Gate::authorize('view', $block);

        return view('blocks.show', ['block' => $block->load('units.rentHistories')]);
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('create', Block::class);
        Gate::authorize('update', $property); // must own the parent property

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'block_code' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);

        $property->blocks()->create($data);

        return back()->with('success', 'Block created.');
    }

    public function update(Request $request, Block $block): RedirectResponse
    {
        Gate::authorize('update', $block);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'block_code' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,archived'],
        ]);

        $block->update($data);

        return back()->with('success', 'Block updated.');
    }

    public function destroy(Block $block): RedirectResponse
    {
        Gate::authorize('delete', $block);

        if ($block->units()->exists()) {
            return back()->with('error', 'Blocks containing units cannot be deleted.');
        }

        $block->delete();

        return back()->with('success', 'Block deleted.');
    }
}
