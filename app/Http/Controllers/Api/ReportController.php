<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\PatientInformation;
use App\Http\Resources\ReportResource;
use App\Http\Resources\PatientInformationResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
class ReportController extends BaseController
{
use FileUploader;
use GeneralTrait;


    public function Report(Request $request)
    {
        try{
            
            $report = reportResource::collection(report::get());
          
            $data =  [
                'report'=>$report

            ];

            return $this->apiResponse($data,true,null,200);


         }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        } 
        
    }
    public function ShowReport(Request $request)
    {
        try {
            $type = $request->input('type');
            $reports = Report::get();
    
            $groupedReports = $reports->groupBy(function ($item) use ($type) {
                $fromDate = Carbon::parse($item->from_date);
                switch ($type) {
                    case 'اسبوعي':
                        return "اسبوع " . $fromDate->weekOfYear;
                    case 'شهري':
                        return "شهر " . $fromDate->month;
                    case 'سنوي':
                        return "عام " . $fromDate->year;
                    default:
                        return $fromDate->format('Y-m-d');
                }
            })->map(function ($items, $groupKey) {
                $totalPatients = $items->sum('number_of_patients');
                $totalBookings = $items->sum('number_of_bookings');
                return [
                    'date' => $groupKey,
                    'total_patients' => $totalPatients,
                    'total_bookings' => $totalBookings,
                ];
            })->values()->toArray();
    
            $data = [
                'report' => $groupedReports,
                'status' => true,
                'error' => null,
                'statusCode' => 200,
            ];
    
            return response()->json($data);
        } catch (\Exception $ex) {
            return response()->json([
                'report' => null,
                'status' => false,
                'error' => $ex->getMessage(),
                'statusCode' => 500,
            ], 500);
        }
    }
    public function storeReport(Request $request)
    {
        try {
            $report = Report::where("type",$request->type)->first();

            if ($report == true) {
                return $this->apiResponse(null,false,"This Report is already exists",402);
            }
            else{
                $uuid = Str::uuid();
                $startDate = Carbon::parse($request->input('from_date'));
                $endDate = Carbon::parse($request->input('to_date'));
                $type = $request->input('type');
                $number_of_patients = $request->input('number_of_patients');
                $number_of_bookings = $request->input('number_of_bookings');
                
                $data =[
                    'from_date'=>$startDate,
                    'to_date'=>$endDate,
                    'type'=> $type,
                    'uuid'=>$uuid, 
                    'number_of_patients'=>0, 
                    'number_of_bookings'=>0, 
                ];}
                if (Report::create($data)) 
                {
                    return $this->apiResponse($data,true,null,200);
                    
                } 
                }catch (\Exception $ex) {
                    return $this->apiResponse(null, false, $ex->getMessage(), 500);
                } 
    }
    public function updateReport(Request $request, Report $report , $uuid)
    {
     
        try{
        $report = Report::where('uuid', $uuid)->firstOrFail();
        
        $report->from_date = $request->from_date;
        $report->to_date = $request->to_date;
        $report->type = $request->type;
        $report->number_of_patients = $request->number_of_patients;
        $report->number_of_bookings = $request->number_of_bookings;

    
    
        if ($report->save()) {
            return $this->apiResponse(null,true,null,200);
        }else{
        return $this->apiResponse(null, false, 'Failed to update the report', 400);
    }
    }catch (\Exception $ex) {
        return $this->apiResponse(null, false, $ex->getMessage(), 500);
    }
    }

    public function destroyReport(report $report,$uuid)
    {
        try{
            $report = Report::where('uuid', $uuid)->firstOrFail();
            if ($report->delete()) {
                return $this->apiResponse(null, true, null, 200);
            }
            return $this->apiResponse(null, false, 'Failed to delete the Report', 400);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, false, $ex->getMessage(), 500);
        }
    }




}
