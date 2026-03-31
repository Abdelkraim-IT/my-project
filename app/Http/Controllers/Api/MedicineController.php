<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Http\Resources\MedicineResource;
use App\Models\Report;
use App\Http\Resources\ReportResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;

class MedicineController extends BaseController
{
    use FileUploader;
    use GeneralTrait;


    public function Medicines(Request $request)
    {
     try{
    
        $medicines = MedicineResource::collection(Medicine::get());

        $medicinesMostRequest = MedicineResource::collection(Medicine::orderBy('order', 'desc')
        ->limit(2) // Limit to top 10 most requested medicines
        ->get());
    
        $result = [
            'Medicine'=>$medicines,
            'Medicine_Most_Request'=>$medicinesMostRequest,
        ];




        return $this->apiResponse($result,'success');
    
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }  
    




}

public function getMostRequestedMedicines(Request $request)
{
 try{

    $medicines = MedicineResource::collection(Medicine::orderBy('order', 'desc')
        ->limit(1) 
        ->get());

        $result = [
            'Medicine_Most_Request'=>$medicines,
        ];

        return $this->apiResponse($result,'success');
    
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }  



}  


}















