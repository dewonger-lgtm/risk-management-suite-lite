<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorrectiveActionRequest;
use App\Http\Requests\UpdateCorrectiveActionRequest;
use App\Services\CorrectiveActionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class CorrectiveActionController
 * 
 * Controller untuk Corrective Action Management.
 */
class CorrectiveActionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private CorrectiveActionService $caService
    ) {}

    /**
     * Display CAs list
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $cas = $this->caService->getAllCAs($companyId);
        $statistics = $this->caService->getCAStatistics($companyId);

        return view('corrective-actions.index', [
            'cas' => $cas,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('corrective-actions.create');
    }

    /**
     * Store new CA
     */
    public function store(StoreCorrectiveActionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->caService->createCA($data);

        return redirect()->route('corrective-actions.index')
            ->with('success', 'Corrective Action created successfully');
    }

    /**
     * Show CA details
     */
    public function show(string $id): View
    {
        $ca = $this->caService->getCAById($id);

        if (!$ca) {
            abort(404, 'Corrective Action not found');
        }

        return view('corrective-actions.show', ['ca' => $ca]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): View
    {
        $ca = $this->caService->getCAById($id);

        if (!$ca) {
            abort(404, 'Corrective Action not found');
        }

        return view('corrective-actions.edit', ['ca' => $ca]);
    }

    /**
     * Update CA
     */
    public function update(UpdateCorrectiveActionRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->caService->updateCA($id, $data);
            return redirect()->route('corrective-actions.show', $id)
                ->with('success', 'Corrective Action updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Start implementation
     */
    public function startImplementation(string $id): RedirectResponse
    {
        try {
            $this->caService->startImplementation($id);
            return redirect()->route('corrective-actions.show', $id)
                ->with('success', 'Implementation started');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Complete implementation
     */
    public function completeImplementation(string $id): RedirectResponse
    {
        try {
            $this->caService->completeImplementation($id);
            return redirect()->route('corrective-actions.show', $id)
                ->with('success', 'Implementation completed');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Verify CA
     */
    public function verify(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'effectiveness_notes' => 'required|string',
        ]);

        try {
            $this->caService->verifyCA($id, $request->effectiveness_notes);
            return redirect()->route('corrective-actions.show', $id)
                ->with('success', 'Corrective Action verified');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete CA
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->caService->deleteCA($id);
            return redirect()->route('corrective-actions.index')
                ->with('success', 'Corrective Action deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get overdue CAs
     */
    public function overdue(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $cas = $this->caService->getOverdueCAs($companyId);

        return view('corrective-actions.overdue', ['cas' => $cas]);
    }
}
