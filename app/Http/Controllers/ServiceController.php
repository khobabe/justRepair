<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Service;
use App\Models\ServiceFees;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view("admin.services.manage");
    }

    public function insert()
    {
        return view("admin.services.insert");
    }

    public function view($id)
    {
        $service = Service::where("slug", $id)->first();
        // dd($service->id);
        return view("admin.services.view", compact("service"));
    }

    public function updateServiceFees(Request $request, $id, $servicefees_id)
    {

        // Find the service fee associated with the service
        $serviceFee = ServiceFees::find($servicefees_id);

        // Check if a service fee exists for this service
        if (!$serviceFee) {
            return redirect()->back();
        }

        // Validate the incoming data for service fee name and fee amount
        $validatedData = $request->validate([
            'service_fees_name' => 'required|string|max:255',  // Name of the service fee
            'service_fees' => 'required|numeric|min:0',        // Fee value, positive number
        ]);

        // Update the service fee details
        $serviceFee->service_fees_name = $validatedData['service_fees_name'];
        $serviceFee->service_fees = $validatedData['service_fees'];

        // Save the updated service fee
        $serviceFee->save();

        return redirect()->back()->with("msg", "updated");
    }

    public function updateRequirements(Request $request, $id)
    {
        $request->validate([
            'req_name' => 'required|string|max:255',
        ]);

        $requirement = Requirement::findOrFail($id);
        $requirement->req_name = $request->req_name;
        $requirement->save();

        return redirect()->back()->with('success', 'Requirement updated successfully.');
    }

    public function storeRequirements(Request $request)
    {
        // dd($request->all()); //debugging, checking the values sent in the request here;

        $request->validate([
            'req_name' => 'required|string|max:255',
            'service_id' => 'required|integer|exists:services,id', // Ensure service_id exists
        ]);

        // Create new requirement with the service_id from the current view context
        Requirement::create([
            'req_name' => $request->req_name,
            'service_id' => $request->service_id, // Pass the service_id from request
        ]);

        return redirect()->back()->with('success', 'Requirement added successfully.');
    }
}
