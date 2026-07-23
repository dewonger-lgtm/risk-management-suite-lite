<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKPIRequest;
use App\Http\Requests\UpdateKPIRequest;
use App\Services\KPIService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class KPIController
 * 
 * Controller untuk KPI Management.
 */
class KPIController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private KPIService $kpiService
    ) {}

    /**
     * Display KPIs list
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $kpis = $this->kpiService->getAllKPIs($companyId);
        $dashboard = $this->kpiService->getDashboardStats($companyId);

        return view('kpis.index', [
            'kpis' => $kpis,
            'dashboard' => $dashboard,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('kpis.create');
    }

    /**
     * Store new KPI
     */
    public function store(StoreKPIRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->kpiService->createKPI($data);

        return redirect()->route('kpis.index')
            ->with('success', 'KPI created successfully');
    }

    /**
     * Show KPI details
     */
    public function show(string $id): View
    {
        $kpi = $this->kpiService->getKPIById($id);

        if (!$kpi) {
            abort(404, 'KPI not found');
        }

        $currentPeriod = now()->format('Y-m');
        $performance = $this->kpiService->getPerformance($id, $currentPeriod);

        return view('kpis.show', [
            'kpi' => $kpi,
            'performance' => $performance,
        ]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): View
    {
        $kpi = $this->kpiService->getKPIById($id);

        if (!$kpi) {
            abort(404, 'KPI not found');
        }

        return view('kpis.edit', ['kpi' => $kpi]);
    }

    /**
     * Update KPI
     */
    public function update(UpdateKPIRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->kpiService->updateKPI($id, $data);
            return redirect()->route('kpis.show', $id)
                ->with('success', 'KPI updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Set KPI target
     */
    public function setTarget(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'period' => 'required|string',
            'target_value' => 'required|numeric|min:0',
        ]);

        try {
            $this->kpiService->setTarget($id, $request->period, $request->target_value);
            return redirect()->route('kpis.show', $id)
                ->with('success', 'Target set successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Record actual value
     */
    public function recordActual(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'period' => 'required|string',
            'actual_value' => 'required|numeric|min:0',
        ]);

        try {
            $this->kpiService->recordActual($id, $request->period, $request->actual_value);
            return redirect()->route('kpis.show', $id)
                ->with('success', 'Actual value recorded successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete KPI
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->kpiService->deleteKPI($id);
            return redirect()->route('kpis.index')
                ->with('success', 'KPI deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
