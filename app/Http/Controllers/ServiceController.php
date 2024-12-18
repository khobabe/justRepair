<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Service;
use App\Models\ServiceFees;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        try {
            // Validate the request with custom error messages
            $validatedData = $request->validate([
                'req_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('requirements')->where(function ($query) use ($request) {
                        return $query->where('service_id', $request->service_id);
                    }),
                ],
                'service_id' => 'required|integer|exists:services,id',
            ], [
                'req_name.required' => 'The requirement name is required.',
                'req_name.unique' => 'This requirement already exists for the selected service.',
                'service_id.exists' => 'The selected service is invalid.',
            ]);

            // Create the new requirement
            Requirement::create($validatedData);

            return redirect()->back()->with('success', 'Requirement added successfully.');
        } catch (ValidationException $e) {
            // Check for a duplicate data error
            if ($e->validator->errors()->has('req_name')) {
                session()->flash('error', $e->validator->errors()->first('req_name'));
                return redirect()->back();
            }

            // Re-throw the exception if it's not a validation error we're handling
            throw $e;
        }
    }



    // requirements delete work goes here
    public function destroyRequirements($id)
    {
        $requirement = Requirement::findOrFail($id);
        $requirement->delete();

        return redirect()->back()->with('success', 'Requirement deleted successfully.');
    }
}
