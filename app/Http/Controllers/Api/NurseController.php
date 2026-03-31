<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Nurse;
use App\Models\NurseDate;
use App\Http\Resources\NurseResource;
use App\Http\Resources\NurseDateResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;

class NurseController extends BaseController
{
    use FileUploader;
    use GeneralTrait;

    public function Nurse(Request $request):JsonResponse
    {
    try{
    
    
        $nurse = NurseResource::collection(Nurse::get());
         $result = [
          'nurse'=>$nurse,
    
           ];
    
        return $this->sendResponse($result,'success');
    
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }  
    
    
    }
    
public function ShowNurse(Request $request, $uuid)
{
    try {
        $nurse = Nurse::where('uuid', $uuid)->first();

        if (!$nurse) {
            return $this->apiResponse(null, false, 'Nurse not found', 404);
        }

        $nurseDates = NurseDateResource::collection(NurseDate::where('nurse_id', $nurse->id)->get());

        $formattedNurseDates = [];
        foreach ($nurseDates as $nurseDate) {
            $formattedNurseDates[] = [
                'day' => $nurseDate->day,
                'from_hour' => $nurseDate->from_hour,
                'to_hour' => $nurseDate->to_hour,
            ];
        }

        $statistics = [
            'number_of_patients' => $nurseDate->number_of_patients,  // Assuming $nurseDate holds the last element
            'number_of_workDay' => $nurseDate->number_of_workDay,
            'number_of_booking' => $nurseDate->number_of_booking,
            'number_of_operations' => $nurseDate->number_of_operations,
        ];

        $result = [
            'nurse' => $nurse,
            'statistics' => $statistics,
            'workDays' => $formattedNurseDates,
        ];

        return $this->sendResponse($result, 'success');
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}




    
  public function storeNurse(Request $request)
    {
       
        try {
            $nurse = Nurse::where("name",$request->name)->first();

            if ($nurse == true) {
                
                return $this->apiResponse(null,false,"This Nurse is already exists",402);
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
                $data =[
                    'name'=>$request->name,
                    'password'=>$request->password,
                    'status'=>$request->status,
                    'phone'=>$request->phone,
                    'image'=> $imageUrl,
                    'Id_Number'=>$randomIdNumber,
                    'uuid'=>$uuid, 
                    'specialization'=>$request->specialization, 
                ];
            }
                if (Nurse::create($data)) 
                {
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

    public function updateNurse(Request $request, Nurse $nurse , $uuid)
    {
    
        try{
        $nurse = Nurse::where('uuid', $uuid)->firstOrFail();
             $base64Image = $request->input('base64Image');
                   $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
                
            }

        $nurse->name = $request->name;
        $nurse->image =  $imageUrl;
        
       
        $nurse->phone = $request->phone;
        $nurse->specialization = $request->specialization;
        
      
        if ($nurse->save()) {
            return $this->apiResponse(null,true,null,200);
        }else{
        return $this->apiResponse(null, false, 'Failed to update the Nurse', 400);
    }
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
    }
    public function storeNurseDate(Request $request, $uuid)
    {
        try {
            $nurse = Nurse::where('uuid', $uuid)->first();
            $nurse_id = $nurse->id;
    
            $existingNurseDate = NurseDate::where('day', $request->day)->first();
    
            if ($existingNurseDate) {
                // Update existing record
                $existingNurseDate->update([
                    'nurse_id'=>$nurse_id,
                    'day' => $request->day,
                    'from_hour' => $request->from_hour,
                    'to_hour' => $request->to_hour,
                    
                ]);
    
                return $this->apiResponse($existingNurseDate, true, "Nurse Date updated successfully", 200);
            } else {
                // Create new record
                $uuid = Str::uuid();
                $data = [
                    'nurse_id'=>$nurse_id,
                    'day' => $request->day,
                    'from_hour' => $request->from_hour,
                    'to_hour' => $request->to_hour,
                    'number_of_patients' => 0,
                    'number_of_workDay' => 1,
                    'number_of_booking' => 0,
                    'number_of_operations' => 0,
                    'uuid'=>$uuid
                ];
    
                $nurseDate = NurseDate::create($data);

                $nurseDate->increment('number_of_workDay');
                
                return $this->apiResponse($data, true, "Nurse Date created successfully", 201); // Use 201 for created resources
                   
                   
                       
                    
                
            }
    
            return $this->apiResponse(null, false, "An error occurred while creating/updating Nurse Date", 500);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

public function destroyNurse(Nurse $nurse , $uuid)
    {
        try{
            $nurse = Nurse::where('uuid', $uuid)->firstOrFail();
            if ($nurse->delete()) {
                return $this->apiResponse(null, true, null, 200);
            }
            return $this->apiResponse(null, false, 'Failed to delete the Nurse', 400);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

    public function searchNurse(Request $request)
    {
        try{

        
        $name = $request->input('name');
        $ID_number = $request->input('ID_number');

        $nurse = NurseResource::collection(Nurse::where('name', 'like', '%' . $name . '%')
        ->Where('ID_Number', 'like', '%' . $ID_Number . '%')
        ->get());

    if ($nurse->isEmpty()) {
        return $this->apiResponse(null, false, 'No Nurse found', 404);
    }
    if ($ID_number) {
        $nurse = NurseResource::collection(Nurse::where('ID_Number', $ID_number)->get());
      } else {
        $nurse = NurseResource::collection(Nurse::where('name', 'like', '%' . $name . '%')->get());
      }
     $data = [
        'Nurse'=>$nurse
     ];
    return $this->apiResponse($data,true,null,200);

    
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
}

}
}
