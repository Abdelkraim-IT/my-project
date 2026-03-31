<?php

namespace App\Http\Controllers\Api;

require __DIR__.'/BaseController.php';
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


use App\Models\Doctor;
use App\Models\Report;
use App\Models\BookingDoctor;
use App\Models\Patient;
use App\Models\Employee;
use App\Models\Nurse;
use App\Models\DoctorDate;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\DoctorDateResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;

class DoctorController extends BaseController

{
    use FileUploader;
    use GeneralTrait;
    
public function index(){
    try{
    $doctor = Doctor::select('id')->where('status', 'في الخدمة')->count();
    $patient = Patient::select('id')->count();
    $employee = Employee::select('id')->where('status', 'في الخدمة')->count();
    $nurse = Nurse::select('id')->where('status', 'في الخدمة')->count();

    $data =[
        'عدد الدكاترة'=>$doctor,
        'عدد المرضى'=>$patient,
        'عدد الموظفين'=>$employee,
        'عدد الممرضين'=>$nurse,
    ];
    return $this->apiResponse($data,true,null,200);
    
    }catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
} 
}

public function Doctor(Request $request):JsonResponse
{
try{


    $doctor = DoctorResource::collection(Doctor::get());
     $result = [
      'doctors'=>$doctor,

       ];

    return $this->sendResponse($result,'success');

}catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
}  


}

public function ShowDoctor(Request $request ,$uuid)
{
try{


    $doctor = DoctorResource::make(Doctor::where('uuid', $uuid))->first();
    $doctor_id = $doctor->id;
    $doctorDates = DoctorDateResource::collection(DoctorDate::where('doctor_id', $doctor_id)->get());

    $formattedDoctorDates = [];


foreach ($doctorDates as $doctorDate) {
  $formattedDoctorDates[] = [
    'day' => $doctorDate->day,
    'from_hour' => $doctorDate->from_hour,
    'to_hour' => $doctorDate->to_hour,
  ];

}
    $statistics = [
      'number_of_patients' => 0,
      'number_of_workDay' => 0,
      'number_of_booking' => 0,
      'number_of_operations' => 0,
    ];

    // Calculate totals (if any schedules exist)
    if ($doctorDates->isNotEmpty()) {
      foreach ($doctorDates as $doctorDate) {
        $statistics['number_of_patients'] += $doctorDate->number_of_patients;
        $statistics['number_of_workDay'] += $doctorDate->number_of_workDay;
        $statistics['number_of_booking'] += $doctorDate->number_of_booking;
        $statistics['number_of_operations'] += $doctorDate->number_of_operations;
      }
    }

$result = [
  'doctor' => $doctor,
  'statistics' => $statistics, 
  'workDays' => $formattedDoctorDates, 
 
];

    return $this->sendResponse($result,'success');

}catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
}  


}


public function storeDoctor(Request $request)
{
  try {
    $doctor = Doctor::where("name", $request->name)->first();

    if ($doctor == true) {
      return $this->apiResponse(null, false, "This Doctor is already exists", 402);
    } else {
      $uuid = Str::uuid();

    $base64Image = $request->input('base64Image');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
                #$imageUrl = $this->uploadFile($request, 'doctors', 'image');
            }
$randomIdNumber = $this->generateRandomIdNumber(); 
      $data = [
        'name' => $request->name,
        'phone' => $request->phone,
        'password' => $request->password,
        'status' => $request->status,
        'image' => $imageUrl,
        'Id_Number' => $randomIdNumber ,
        'uuid' => $uuid,
        'specialization' => $request->specialization,
      ];
      

      if (Doctor::create($data)) {
        return $this->apiResponse($data, true, null, 200);
      }
    }

    return $this->apiResponse(null, false, "An error occurred while creating Doctor", 500);
  } catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
  }
}
private function generateRandomIdNumber(): int
{
    // Generate a random number in the range 100000 to 999999
    $randomNumber = random_int(100000, 999999);

    // Return the random integer
    return $randomNumber;
}




public function BookingDoctor(Request $request)
{
    try {
        $date = Carbon::parse($request->input('date'));
        $patientId_number = $request->input('ID_numberPatient');
        $doctorID_number = $request->input('ID_numberDoctor');

        $doctor = Doctor::where('Id_Number', $doctorID_number)->first();
        $doctor_id = $doctor->id;

        $patient = Patient::where('ID_number', $patientId_number)->first();
        $patient_id = $patient->id;

        $doctorDate = DoctorDate::where('doctor_id', $doctor_id)->first();
        $doctorDate_id = $doctorDate->id;

        $uuid = Str::uuid();
        $data = [
            'uuid' => $uuid,
            'doctor_dates_id' => $doctorDate_id,
            'patient_id' => $patient_id,
            'date' => $date
        ];

        if (BookingDoctor::create($data)) {
            $doctorDate = DoctorDate::where('id', $doctorDate_id)->first();
            if ($doctorDate) {
                $doctorDate->increment('number_of_patients');
                $doctorDate->increment('number_of_booking');
                $doctorDate->save();
            }

            $report = Report::where('from_date', $date->format('Y-m-d'))->first();
            if ($report) {
                $report->increment('number_of_bookings');
                $report->increment('number_of_patients');
                $report->save();
            } else {
                $reportdata = [
                    'uuid' => Str::uuid(),
                    'from_date' => $date->format('Y-m-d'),
                    'number_of_patients' => 1,
                    'number_of_bookings' => 1,
                ];
                Report::create($reportdata);
            }

            return $this->apiResponse($data, true, null, 200);
        }
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}

    public function updateDoctor(Request $request, $uuid)
            {
                try {
                    $doctor = Doctor::where('uuid', $uuid)->firstOrFail();
            
                    // Handle Base64 image (optional)
               $base64Image = $request->input('base64Image');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
                #$imageUrl = $this->uploadFile($request, 'doctors', 'image');
            }
            
                    $doctor->name = $request->name;
                    $doctor->status = $request->status;
                    $doctor->phone = $request->phone;
                    $doctor->image = $imageUrl ;
                    $doctor->specialization = $request->specialization;
            
                    if ($doctor->save()) {
                        return $this->apiResponse($doctor, true, null, 200); // Return updated doctor data
                    } else {
                        return $this->apiResponse(null, false, 'Failed to update the Doctor', 400);
                    }
                } catch (\Exception $ex) {
                    return $this->apiResponse(null, false, $ex->getMessage(), 500);
                }
            }

   public function destroyDoctor(Doctor $doctor, $uuid)
{
  try {
    // Retrieve doctor using injected Doctor object
    $doctor = Doctor::where('uuid', $uuid)->firstOrFail();

    $status = $doctor->status;

    if ($status === "في الخدمة") {
      // Update status to "خارج الخدمة" using an associative array
      $doctor->update(['status' => 'خارج الخدمة']);
      return $this->apiResponse(null, true, 'Doctor deactivated (in service)', 200);

    } else {
      $doctor->delete();
      return $this->apiResponse(null, true, 'Doctor deleted successfully', 200);
    }

  } catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
  }
}

public function storeDoctorDate(Request $request, $uuid)
{
    try {
        $doctor = Doctor::where('uuid', $uuid)->first();
        $doctor_id = $doctor->id;

        $existingDoctorDate = DoctorDate::where('doctor_id', $doctor_id)->where('day', $request->day)->first();

        if ($existingDoctorDate) {
            // Update existing DoctorDate
            $existingDoctorDate->update([
                'from_hour' => $request->from_hour,
                'to_hour' => $request->to_hour,
            ]);

            return $this->apiResponse($existingDoctorDate, true, "Doctor Date updated successfully", 200);
        } else {
            // Create new DoctorDate
            $data = [
                'doctor_id' => $doctor_id,
                'day' => $request->day,
                'from_hour' => $request->from_hour,
                'to_hour' => $request->to_hour,
                'number_of_workDay' => 1, // Initialize to 1 for new record
                'number_of_patients' => 0,
                'number_of_booking' => 0,
                'number_of_operations' => 0,
                'uuid' => Str::uuid(),
            ];

            $doctorDate = DoctorDate::create($data);

            // Increment number_of_workDay immediately after creation
            $doctorDate->increment('number_of_workDay');

            return $this->apiResponse($doctorDate, true, "Doctor Date created successfully", 201);
        }
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}


    public function updateDoctorDate(Request $request, DoctorDate $doctorDate , $uuid)
    {
      
    
    try{
        $doctorDate = DoctorDate::where('uuid', $uuid)->firstOrFail();
        
        $doctorDate->day = $request->day;
        $doctorDate->from_hour = $request->from_hour;
        $doctorDate->to_hour = $request->to_hour;
        $doctorDate->number_of_patients = $request->number_of_patients;
        $doctorDate->number_of_workDay = $request->number_of_workDay;
        $doctorDate->number_of_booking = $request->number_of_booking;
        $doctorDate->number_of_operations = $request->number_of_operations;


    
        if ($doctorDate->save()) {
            return $this->apiResponse(null,true,null,200);
        }else{
        return $this->apiResponse(null, false, 'Failed to update the DoctorDate', 400);
    }
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
    }

    public function destroy(DoctorDate $doctorDate , $uuid)
    {
        try{
            $doctorDate = DoctorDate::where('uuid', $uuid)->firstOrFail();
            if ($doctorDate->delete()) {
                return $this->apiResponse(null, true, null, 200);
            }
            return $this->apiResponse(null, false, 'Failed to delete the DoctorDate', 400);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
        
    }
    public function searchDoctor(Request $request)
    {
        try{

        
        $name = $request->input('name');
        $ID_number = $request->input('ID_number');

        $doctor = DoctorResource::collection(Doctor::where('name', 'like', '%' . $name . '%')
        ->Where('ID_number', 'like', '%' . $ID_number . '%')
        ->get());

    if ($doctor->isEmpty()) {
        return $this->apiResponse(null, false, 'No doctor found', 404);
    }
    if ($ID_number) {
  $doctor = DoctorResource::collection(Doctor::where('ID_number', $ID_number)->get());
} else {
  $doctor = DoctorResource::collection(Doctor::where('name', 'like', '%' . $name . '%')->get());
}
     $data = [
        'Doctor'=>$doctor
     ];
    return $this->apiResponse($data,true,null,200);

    
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
}

}


}
