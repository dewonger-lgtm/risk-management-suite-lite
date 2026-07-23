<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRiskRequest;
use App\Http\Requests\UpdateRiskRequest;
use App\Services\RiskService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class RiskController
 * 
 * Controller untuk Risk Management.
 * Menangani HTTP requests dan responses.
 */
class RiskController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private RiskService $riskService
    ) {}

    /**
     * Display risks list
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $risks = $this->riskService->getAllRisks($companyId);
        $statistics = $this->riskService->getRiskStatistics($companyId);

        return view('risks.index', [
            'risks' => $risks,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('risks.create');
    }

    /**
     * Store new risk
     */
    public function store(StoreRiskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['creator_id'] = auth()->id();

        $this->riskService->createRisk($data);

        return redirect()->route('risks.index')
            ->with('success', 'Risk created successfully');
    }

    /**
     * Show risk details
     */
    public function show(string $id): View
    {
        $risk = $this->riskService->getRiskById($id);

        if (!$risk) {
            abort(404, 'Risk not found');
        }

        return view('risks.show', ['risk' => $risk]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): View
    {
        $risk = $this->riskService->getRiskById($id);

        if (!$risk) {
            abort(404, 'Risk not found');
        }

        return view('risks.edit', ['risk' => $risk]);
    }

    /**
     * Update risk
     */
    public function update(UpdateRiskRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->riskService->updateRisk($id, $data);
            return redirect()->route('risks.show', $id)
                ->with('success', 'Risk updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete risk
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->riskService->deleteRisk($id);
            return redirect()->route('risks.index')
                ->with('success', 'Risk deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get high risks for dashboard
     */
    public function highRisks(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $risks = $this->riskService->getHighRisks($companyId);

        return view('risks.high-risks', ['risks' => $risks]);
    }
}
