<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Illness;
use App\Models\Patient;
use App\Models\PatientIllness;
use App\Http\Resources\IllnessResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientIllnessResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
class IllnessController extends Controller
{
    
    use FileUploader;
    use GeneralTrait;

    //add illness for patient uuid, name ,danger
    //get illness for patient (return in show patient )
    // report for illness most
    //report date



    public function storeIllness(Request $request){
        try{
        $uuidPatient = $request->input('uuid');
        #$illness = Illness::where('name',$request->name)->first();

   
        $illnesses = $request->input('illness');
      
        foreach ($illnesses as $illness) {
            $name = $illness['name'];
            $danger = $illness['danger'];
        
            $data =[
                'uuid'=>Str::uuid(),
                'name'=>$name,
                'danger'=>$danger,
            
            ];
            
        }

 $illness = Illness::create($data);
$patient = Patient::where('uuid', $uuidPatient)->first();
$patientId = $patient->id;

 if ($patientId) {
    PatientIllness::create([
        'uuid'=>Str::uuid(),
        'patient_id' => $patientId,
        'illness_id' => $illness->id,
    ]);
}
return $this->apiResponse($data,true,null,200);



    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
}   


 public function storePatientIllness(Request $request){
    try{
        $uuidPatient = $request->input('uuidPatient');
        $uuidIllness = $request->input('uuidIllness');
        $patient = Patient::where('uuid', $uuidPatient)->firstOrFail();
        $illness = Illness::where('uuid', $uuidIllness)->firstOrFail();
 
    $patient_id = $patient->id;
    $illness_id = $illness->id;


$data =[
'uuid'=>Str::uuid(),
'patient_id'=>$patient_id,
'illness_id'=>$illness_id,

];
   PatientIllness::create($data);

 return $this->apiResponse($data,true,null,200);

}catch (\Exception $ex) {
    return $this->apiResponse(null, false, $ex->getMessage(), 500);
}
}


}