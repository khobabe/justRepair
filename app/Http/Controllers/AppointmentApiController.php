<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::query();

        // Filter appointments based on the date range
        $dateRange = $request->get('dateRange');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($dateRange === 'last_week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($dateRange === 'last_month') {
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month);
        } elseif ($dateRange === 'this_year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($dateRange === 'custom') {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');
    
            // Ensure both start date and end date are provided
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [Carbon::parse($startDate), Carbon::parse($endDate)]);
            }
        }
    
        // Search appointments based on the search term
        $searchTerm = $request->get('search');
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('fullname', 'like', '%' . $searchTerm . '%')
                    ->orWhere('mobileno', 'like', '%' . $searchTerm . '%')
                    ->orWhere('complain_no',$searchTerm)
                    ;
            });
        }
    
        // Fetch appointments
        $appointments = $query->get();    
        return response()->json($appointments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
       
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'mobileno' => 'required|string',
            'address' => 'required|string',
            'landmark' => 'required|string',
            'city' => 'required|string',
            'requirements' => 'required|array',
            'requirements.*' => 'string|nullable',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string',
            'service_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Generating Complain Number
        
        do {
            $complain_no = 'JR' . date('ymd') . mt_rand(1000, 9999);
            // Check if the generated unique number already exists in the database
            $existing = Appointment::where('complain_no', $complain_no)->exists();
        } while ($existing);

        // Store the service
        $appointment = new appointment();
        $appointment->fullname = $request->fullname;
        $appointment->complain_no = $complain_no;
        $appointment->mobileno = $request->mobileno;
        $appointment->address = $request->address;
        $appointment->landmark = $request->landmark;
        $appointment->city = $request->city;
        $appointment->service_id = $request->service_id;
        $appointment->requirements = json_encode($request->requirements); // Assuming 'requirements' is a JSON field

        $appointment->preferred_date = $request->preferred_date;
        $appointment->preferred_time = $request->preferred_time;

        $appointment->save();

        session()->flash('complain_no', $complain_no);
      
        return response()->json(['success' => true,'data'=>$appointment,'msg'=>'Appointment has been successfully Booked']);

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::where("id",$id)->with("services")->first();
        return response()->json($appointment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function searchComplain($request)
    {
        $searchResults =  Appointment::where("complain_no",$request)->get();
        return response()->json(['data' => $searchResults]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:appointments,id',
            'status' => 'required|in:accept,reject,process,done,close',
        ]);

        $appointment = Appointment::findOrFail($request->id);
        $appointment->status = $request->status;
        $appointment->save();

        return response()->json(['message' => 'Appointment status updated successfully', "data" => $appointment], 200);
    }

}