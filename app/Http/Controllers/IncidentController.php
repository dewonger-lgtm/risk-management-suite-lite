<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Services\IncidentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class IncidentController
 * 
 * Controller untuk Incident Management.
 */
class IncidentController extends Controller
{
    /**
     * Constructor
     */
    public function __construct(
        private IncidentService $incidentService
    ) {}

    /**
     * Display incidents list
     */
    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $incidents = $this->incidentService->getAllIncidents($companyId);
        $statistics = $this->incidentService->getIncidentStatistics($companyId);

        return view('incidents.index', [
            'incidents' => $incidents,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        return view('incidents.create');
    }

    /**
     * Store new incident
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['reported_by'] = auth()->id();

        $this->incidentService->createIncident($data);

        return redirect()->route('incidents.index')
            ->with('success', 'Incident reported successfully');
    }

    /**
     * Show incident details
     */
    public function show(string $id): View
    {
        $incident = $this->incidentService->getIncidentById($id);

        if (!$incident) {
            abort(404, 'Incident not found');
        }

        return view('incidents.show', ['incident' => $incident]);
    }

    /**
     * Show edit form
     */
    public function edit(string $id): View
    {
        $incident = $this->incidentService->getIncidentById($id);

        if (!$incident) {
            abort(404, 'Incident not found');
        }

        return view('incidents.edit', ['incident' => $incident]);
    }

    /**
     * Update incident
     */
    public function update(UpdateIncidentRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->incidentService->updateIncident($id, $data);
            return redirect()->route('incidents.show', $id)
                ->with('success', 'Incident updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Assign investigator
     */
    public function assignInvestigator(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'investigator_id' => 'required|uuid|exists:users,id',
        ]);

        try {
            $this->incidentService->assignInvestigator($id, $request->investigator_id);
            return redirect()->route('incidents.show', $id)
                ->with('success', 'Investigator assigned successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete incident
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->incidentService->deleteIncident($id);
            return redirect()->route('incidents.index')
                ->with('success', 'Incident deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
