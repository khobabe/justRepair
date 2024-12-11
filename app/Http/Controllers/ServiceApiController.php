<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Service;
use App\Models\ServiceFees;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Service::with("requirements")->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
        ]);


        // Store the service
        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        // Handle file upload
        if ($request->hasFile('icon')) {
            $iconName = time() . '.' . $request->icon->getClientOriginalExtension();
            $request->icon->move(public_path('uploads'), $iconName);
            $service->icon = $iconName;
        }
        $service->save();

        $requirements = $request->input('requirements');
        foreach ($requirements as $requirement) {
            $req = new Requirement();
            $req->service_id = $service->id; // Assuming there's a 'service_id' column in the 'requirements' table
            $req->req_name = $requirement;
            $req->save();
        }

        return response()->json(['success' => true]);
    }


    public function show(string $slug)
    {
        $data = Service::where("slug", $slug)->with("requirements")->first();
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function update(Request $request, string $slug)
    {
        // Find the service by its slug
        $service = Service::where("slug", $slug)->first();

        // Check if the service exists
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|array',
            'requirements.*.id' => 'nullable|exists:requirements,id',
            'requirements.*.req_name' => 'required|string|max:255',
        ]);

        // Update the service fields
        $service->name = $validatedData['name'];
        $service->description = $validatedData['description'];
        $service->save(); // Save the updated service

        // Delete requirements that are not in the updated list
        $existingIds = collect($validatedData['requirements'])->pluck('id')->filter();
        Requirement::where('service_id', $service->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Update the requirements
        if (isset($validatedData['requirements'])) {
            foreach ($validatedData['requirements'] as $reqData) {
                if (isset($reqData['id'])) {
                    // Update existing requirement
                    $requirement = Requirement::find($reqData['id']);
                    if ($requirement) {
                        $requirement->req_name = $reqData['req_name'];
                        $requirement->save();
                    }
                } else {
                    // Create a new requirement (if no ID provided)
                    Requirement::create([
                        'service_id' => $service->id,
                        'req_name' => $reqData['req_name'],
                    ]);
                }
            }
        }

        // Return a success response
        return response()->json(['message' => 'Service updated successfully', 'service' => $service]);
    }




    public function destroy(string $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Delete the service
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
