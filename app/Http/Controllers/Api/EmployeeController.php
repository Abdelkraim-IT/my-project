<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeDate;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeDateResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;

class EmployeeController extends BaseController

{
    use FileUploader;
    use GeneralTrait;

    public function Employee(Request $request):JsonResponse
{
try{


    $employee = EmployeeResource::collection(Employee::get());
     $result = [
      'employees'=>$employee,

       ];

    return $this->sendResponse($result,'success');

}catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
}  


}

public function ShowEmployee(Request $request, $uuid)
{
    try {
        $employee = Employee::where('uuid', $uuid)->first();

        if (!$employee) {
            return $this->apiResponse(null, false, 'Employee not found', 404);
        }

        $employeeDates = EmployeeDateResource::collection(EmployeeDate::where('employee_id', $employee->id)->get());

        $formattedEmployeeDates = [];
        foreach ($employeeDates as $employeeDate) {
            $formattedEmployeeDates[] = [
                'day' => $employeeDate->day,
                'from_hour' => $employeeDate->from_hour,
                'to_hour' => $employeeDate->to_hour,
            ];
        }

          $statistics = [
      'number_of_patients' => 0,
      'number_of_workDay' => 0,
      'number_of_booking' => 0,
      'number_of_operations' => 0,
    ];

    // Calculate totals (if any schedules exist)
    if ($employeeDates->isNotEmpty()) {
      foreach ($employeeDates as $employeeDate) {
        $statistics['number_of_patients'] += $employeeDate->number_of_patients;
        $statistics['number_of_workDay'] += $employeeDate->number_of_workDay;
        $statistics['number_of_booking'] += $employeeDate->number_of_booking;
        $statistics['number_of_operations'] += $employeeDate->number_of_operations;
      }
    }

        $result = [
            'employee' => $employee,
            'statistics' => $statistics,
            'workDays' => $formattedEmployeeDates,
        ];

        return $this->sendResponse($result, 'success');
    } catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}








    public function storeEmployee(Request $request)
    {
       
        try {
            $employee = Employee::where("name",$request->name)->first();

            if ($employee == true) {
                
                return $this->apiResponse(null,false,"This Employee is already exists",402);
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
                    'ID_number'=> $randomIdNumber,
                    'status'=>$request->status,
                    'phone'=>$request->phone,
                    'image'=> $imageUrl,
                    'job'=>$request->job,
                    'salary'=>$request->salary,
                    'uuid'=>$uuid, 
                    
                ];
            }
                if (Employee::create($data)) 
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

    public function updateEmployee(Request $request, Employee $employee , $uuid)
    {
    
        try{

        $employee = Employee::where('uuid', $uuid)->firstOrFail();
                    $base64Image = $request->input('base64Image');
            $imageUrl = null;

            if ($base64Image) {
                // Handle Base64 image
                $imageUrl = $this->handleBase64Image($base64Image);
            } else if ($request->hasFile('image')) {
                // Handle file upload
                
            }

        $employee->name = $request->name;
        $employee->salary = $request->salary;
        $employee->phone = $request->phone;
        $employee->status = $request->status;
        $employee->job = $request->job;
        $employee->image =  $imageUrl;
        
        
        
      
        if ($employee->save()) {
            return $this->apiResponse(null,true,null,200);
        }else{
        return $this->apiResponse(null, false, 'Failed to update the Employee', 400);
    }
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
    }
        public function storeEmployeeDate(Request $request, $uuid)
    {
        try {
            $employee = Employee::where('uuid', $uuid)->first();
            $employee_id = $employee->id;
    
            $existingEmployeeDate = EmployeeDate::where('day', $request->day)->first();
     $number_of_patients = 0;
           $number_of_workDay = 1;
           $number_of_booking = 0;
           $number_of_operations = 0;
            if ($existingEmployeeDate) {
                // Update existing record
                $existingEmployeeDate->update([
                    'employee_id'=>$employee_id,
                    'day' => $request->day,
                    'from_hour' => $request->from_hour,
                    'to_hour' => $request->to_hour,
                    
                ]);
    
                return $this->apiResponse($existingEmployeeDate, true, "Employee Date updated successfully", 200);
            } else {
                // Create new record
                $uuid = Str::uuid();
                $data = [
                    'employee_id'=>$employee_id,
                    'day' => $request->day,
                    'from_hour' => $request->from_hour,
                    'to_hour' => $request->to_hour,
                    'number_of_patients' => $number_of_patients,
                    'number_of_workDay' => $number_of_workDay,
                    'number_of_booking' => $number_of_booking,
                    'number_of_operations' => $number_of_operations,
                    'uuid'=>$uuid
                ];
    
                $EmployeeDate = EmployeeDate::create($data);
                $EmployeeDate->increment('number_of_workDay');
                return $this->apiResponse($data, true, "Employee Date created successfully", 201); 
                
                   
               
                
            }
    
            return $this->apiResponse(null, false, "An error occurred while creating/updating Employee Date", 500);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

public function destroyEmployee(Employee $employee , $uuid)
    {
        try{
            $employee = Employee::where('uuid', $uuid)->firstOrFail();
            if ($employee->delete()) {
                return $this->apiResponse(null, true, null, 200);
            }
            return $this->apiResponse(null, false, 'Failed to delete the Employee', 400);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }

    public function searchEmployee(Request $request)
    {
        try{

        
        $name = $request->input('name');
        $ID_number = $request->input('ID_number');

        $employee = EmployeeResource::collection(Employee::where('name', 'like', '%' . $name . '%')
        ->Where('ID_number', 'like', '%' . $job . '%')
        ->get());

    if ($employee->isEmpty()) {
        return $this->apiResponse(null, false, 'No Employee found', 404);
    }
    if ($ID_number) {
        $employee = EmployeeResource::collection(Employee::where('ID_number', $ID_number)->get());
      } else {
        $employee = EmployeeResource::collection(Employee::where('name', 'like', '%' . $name . '%')->get());
      }
     $data = [
        'Employee'=>$employee
     ];
    return $this->apiResponse($data,true,null,200);

    
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
}

}
}
