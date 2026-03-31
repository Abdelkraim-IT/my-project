<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Report;
use App\Models\Illness;
use App\Models\PatientIllness;
use App\Models\Medicine;
use App\Models\PatientInformation;
use App\Http\Resources\PatientResource;
use App\Http\Resources\IllnessResource;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PatientInformationResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;

class PatientController extends BaseController

{
    use FileUploader;
    use GeneralTrait;

    public function Patient(Request $request):JsonResponse
    {
    try{
    
    
        $patient = PatientResource::collection(Patient::get());
    
        
       $result = [
    'patients' => $patient,
       
       

];
    
        return $this->sendResponse($result,'success');
    
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }  
    
    
    }
        public function Patientindex(Request $request):JsonResponse
    {
    try{
    
    
        $patient1 = PatientResource::collection(Patient::where('sex','male')->where('age','<=','18')->get());
        $patient2 = PatientResource::collection(Patient::where('sex','Female')->where('age','<=','18')->get());
        $patient3 = PatientResource::collection(Patient::where('sex','male')->where('age',[18,55])->get());
        $patient4 = PatientResource::collection(Patient::where('sex','Female')->where('age',[18,55])->get());
        $patient5 = PatientResource::collection(Patient::where('sex','male')->where('age','>=','55')->get());
        $patient6 = PatientResource::collection(Patient::where('sex','Female')->where('age','>=','55')->get());
        
       $result = [
    'under_18' => [
        'male' => PatientResource::collection(Patient::where('sex', 'male')->where('age', '<=', 18)->get()),
        'female' => PatientResource::collection(Patient::where('sex', 'female')->where('age', '<=', 18)->get()),
    ],
    'between_18_55' => PatientResource::collection(Patient::where('age', '>=', 18)->where('age', '<=', 55)->get()),
    'over_55' => [
        'male' => PatientResource::collection(Patient::where('sex', 'male')->where('age', '>', 55)->get()),
        'female' => PatientResource::collection(Patient::where('sex', 'female')->where('age', '>', 55)->get()),
    ],
];
    
        return $this->sendResponse($result,'success');
    
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }  
    
    
    }
    
public function ShowPatient(Request $request, $uuid)
{
    try {
        $reports = [];  
        $data = [];  

        $patients = PatientResource::make(Patient::where('uuid', $uuid))->first();
        $patient_ids = $patients->id;  

        $patientInformations = PatientInformationResource::collection(PatientInformation::where('patient_id', $patient_ids)->get());

        foreach ($patientInformations as $patientInformation) {
            
            $report = [ 
              'uuid' => $patientInformation['uuid'],
           'patient_id' => $patientInformation['patient_id'],
         'files' => $patientInformation['files'],
        'notes' => $patientInformation['notes'],
      'created_at' => $patientInformation['created_at'],
        'updated_at' => $patientInformation['updated_at'],
            ];
            $reports[] = $report;  

            $analysisData = [];  
            $analysisData = array_filter(
                $patientInformation->toArray(),
                function ($item, $key) {
                    return in_array($key, ['blood_pressure', 'Body_temperature', 'Heart_rate', 'Breathing_rate']);
                },
                ARRAY_FILTER_USE_BOTH
            );

            $analysisData = array_map(
                function ($item, $key) {
                    return [
                        'title' => ucfirst(str_replace('_', ' ', $key)),
                        'value' => $item,
                    ];
                },
                $analysisData,
                array_keys($analysisData)
            );
$medicine = MedicineResource::collection(Medicine::where('patient_id',$patient_ids)->get());
#$illness = IllnessResource::collection(Illness::where('patient_id',$patient_ids)->get());
            $data = [
                'patients' => $patients,
                'report' => $reports,  
                'Analysis' => $analysisData,
                'Medicine'=> $medicine,
                'mostCommonIllness'=>$mostCommonIllness = $this->getMostCommonIllness($patient_ids),
            ];
        }

        return $this->apiResponse($data,true,null,200);
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}

private function getMostCommonIllness($patient_ids)
{
    $patientIllnesses = PatientIllness::where('patient_id', $patient_ids)->get();

   
    $illnessCounts = [];
    foreach ($patientIllnesses as $patientIllness) {
        $illnessId = $patientIllness->illness_id;
        if (!isset($illnessCounts[$illnessId])) {
            $illnessCounts[$illnessId] = 0;
        }
        $illnessCounts[$illnessId]++;
    }


    $mostCommonIllnessId = null;
    $highestCount = 0;
    foreach ($illnessCounts as $illnessId => $count) {
        if ($count > $highestCount) {
            $mostCommonIllnessId = $illnessId;
            $highestCount = $count;
        }
    }

  
    if ($mostCommonIllnessId !== null) {
        $mostCommonIllness = Illness::find($mostCommonIllnessId);
        return $mostCommonIllness;
    } else {
        return null; // No illness records found for the patient
    }
}


    public function storePatient(Request $request)
    {
       
        try {
            $patient = patient::where('name',$request->name)->first();

            if ($patient == true) {
                
                return $this->apiResponse(null,false,"This patient is already exists",402);
            }
            else{
                $uuid = Str::uuid();
                 $base64Image = $request->input('base64Image');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
        
            }
             
                $randomIdNumber = $this->generateRandomIdNumber();
                $name = $request->input('name');
                $data =[
                    'name'=>$name,
                    'password'=>$request->password,
                    'phone'=>$request->phone,
                    'age'=>$request->age,
                    'image'=> $imageUrl,
                    'address'=>$request->address,
                    'gender'=>$request->gender,
                    'length'=>$request->length,
                    'weight'=>$request->weight,   
                    'uuid'=>$uuid, 
                    'ID_number'=> $randomIdNumber, 
                ];
            }
                if (Patient::create($data)) 
                {
                   
                    $report = Report::orderby('created_at')->first();

                    if ($report) {
                        DB::transaction(function () use ($report) {
                            $report->increment('number_of_patients');
                            $report->save();
                            
                        });
                    }
                                     
                    return $this->apiResponse($data,true,null,200);
                } 
                }catch (\Exception $ex) {
                    return $this->apiResponse(null, false, $ex->getMessage(), 500);
                } 
    }
        private function generateRandomIdNumber(): int
{
    // Generate a random number in the range 100000 to 999999
    $randomNumber = random_int(10000, 99999);

    // Return the random integer
    return $randomNumber;
}

    public function updatePatient(Request $request, patient $patient , $uuid)
    {
    
        try{
        $patient = patient::where('uuid', $uuid)->firstOrFail();
     $base64Image = $request->input('base64Image');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
                #$imageUrl = $this->uploadFile($request, 'doctors', 'image');
            }

        $patient->name = $request->name;
        $patient->password = $request->password;
        $patient->phone = $request->phone;
        $patient->age = $request->age;
        $patient->image =  $imageUrl;
        $patient->address = $request->address;
        $patient->gender = $request->gender;
        $patient->length = $request->length;
        $patient->weight = $request->weight;
        $patient->ID_number = $request->ID_number;

      
        if ($patient->save()) {
            return $this->apiResponse(null,true,null,200);
        }else{
        return $this->apiResponse(null, false, 'Failed to update the patient', 400);
    }
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
    }

public function destroyPatient(patient $patient , $uuid)
    {
        try{
            $doctorDate = patient::where('uuid', $uuid)->firstOrFail();
            if ($doctorDate->delete()) {
                return $this->apiResponse(null, true, null, 200);
            }
            return $this->apiResponse(null, false, 'Failed to delete the patient', 400);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

    public function searchPatient(Request $request)
    {
        try{

        
        $name = $request->input('name');
        $ID_number = $request->input('ID_number');

        $patient = PatientResource::collection(Patient::where('name', 'like', '%' . $name . '%')
        ->Where('ID_number', 'like', '%' . $ID_number . '%')
        ->get());

    if ($patient->isEmpty()) {
        return $this->apiResponse(null, false, 'No patient found', 404);
    }
    if ($ID_number) {
        $patient = PatientResource::collection(Patient::where('ID_number', $ID_number)->get());
      } else {
        $patient = PatientResource::collection(Patient::where('name', 'like', '%' . $name . '%')->get());
      }
     $data = [
        'patient'=>$patient
     ];
    return $this->apiResponse($data,true,null,200);

    
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
}

}

public function PatientInformation(Request $request, PatientInformation $patientInformation): JsonResponse
{
    try {
        $patientInformations = Patient::with('PatientInformation')
            ->get()
            ->map(function ($patient) {
                $ageGroup = $patient->age <= 18 ? 'boys' : ($patient->age <= 55 ? 'young' : 'Older_adults');
                $gender = $patient->gender === 'male' ? 'male' : 'female';

                return [
                    'patient' => [
                        'uuid' => $patient->uuid,
                        'name' => $patient->name,
                        'age' => $patient->age,
                        'gender' => $patient->gender,
                    ],
                    'patientInformation' => $patient->PatientInformation,
                    'ageGroup' => $ageGroup,
                    'gender' => $gender,
                ];
            });

        $data = [
            'boys' => [
                'male' => [],
                'female' => [],
            ],
            'young' => [],
            'Older_adults' => [
                'male' => [],
                'female' => [],
            ],
        ];

        foreach ($patientInformations as $patientInformation) {
            $data[$patientInformation['ageGroup']][$patientInformation['gender']][] = $patientInformation;
        }

        return response()->json($data, 200);
    } catch (\Exception $ex) {
        return response()->json([
            'message' => $ex->getMessage(),
        ], 500);
    }
}
public function storePatientInformation(Request $request)
{
    try {
        $patientInformation = PatientInformation::where("notes", $request->notes)->first();

       

        $uuid = Str::uuid();
              $base64Image = $request->input('filebase64Url');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('filebase64Url')) {
                // Handle file upload
                
            }
        $medicinesData = $request->input('medicines');
        $illnessData = $request->input('illness');
        $patientName = $request->input('name');
        $patient = Patient::where('name' , $patientName)->firstOrFail();
       
        $patient_id = $patient->id;

        $bloodPressureValues = explode("/", $request->blood_pressure);

        $data = [
            'patient_id' => $patient_id,
            'notes' => $request->notes,
            'files' => $imageUrl,
            'blood_pressure' => $bloodPressureValues[0],
            'Body_temperature' => $bloodPressureValues[1],
            'Heart_rate' => $request->Heart_rate,
            'Breathing_rate' => $request->Breathing_rate,
            'uuid' => $uuid,
            #'medicine'=> MedicineResource::collection(Medicine::where('patient_id',$patient_id)->get())
        ];

        $patientInformation = PatientInformation::create($data);
        $medicine = Medicine::where('patient_id',$patient_id)->get();
        $this->storePatientMedicines($medicinesData, $patient_id);
        return $this->apiResponse($data,  true, null, 200);
     
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}
private function storePatientMedicines($medicinesData, $patient_id)
{
  
    foreach ($medicinesData as $medicine) {
        $name = $medicine['name'];
        $price = $medicine['price'];
        $minAge = $medicine['minage'];
        $maxAge = $medicine['maxage'];
        $notes = $medicine['notes'];
        $order = 1;

        $data = [
            'uuid' => Str::uuid(),
            'name' => $name,
            'order' => $order,
            'patient_id' => $patient_id,
            'price' => $price,
            'minage' => $minAge,
            'maxage' => $maxAge,
            'notes' => $notes,
        ];

        $medicine = Medicine::where('patient_id', $patient_id)->where('name', $name)->first();
        if ($medicine) {
            $medicine->increment('order');
            
        } else {
            Medicine::create($data);
            
        } 
    }
}
public function updatePatientInformation(Request $request, PatientInformation $patientInformation , $uuid)
{
 

    try{
    $patientInformation = patientInformation::where('uuid', $uuid)->firstOrFail();
    $file_url = $this->uploadFile($request,  'patient_information', 'files');

    
    $patient_id = $patientInformation->patient_id;

    $patientInformation->patient_id = $patient_id;
    $patientInformation->notes = $request->notes;
    $patientInformation->files =   $file_url ;
    

   

    if ($patientInformation->save()) {
        return $this->apiResponse(null,true,null,200);
    }else{
    return $this->apiResponse(null, false, 'Failed to update the patientInformation', 400);
}
}catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
}
}


public function destroyPatientInformation(patientInformation $patientInformation , $uuid)
{
    try{
        $patientInformation = patientInformation::where('uuid', $uuid)->firstOrFail();
        if ($patientInformation->delete()) {
            return $this->apiResponse(null, true, null, 200);
        }
        return $this->apiResponse(null, false, 'Failed to delete the patientInformation', 400);
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}



}
