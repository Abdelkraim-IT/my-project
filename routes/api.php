<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\NurseController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\IllnessController;
use App\Http\Middleware\JsonMiddleware;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/






Route::get('test-online', function () {
    return 'You are online!';
});

Route::middleware( 'auth:sanctum')->get('test-online', function () {
    return 'You are online!';
});
Route::group(['middleware' => [\App\Http\Middleware\CorsHeader::class]], function () {

  // Register routes
  Route::prefix('Register')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [RegisterController::class, 'login']);
    Route::get("/logout", [RegisterController::class, 'logout'])->middleware('auth:sanctum');
  });



    Route::prefix('Doctor')->group(function () {
        Route::get("/index", [DoctorController::class, 'index'])->middleware('auth:sanctum');
        Route::post("/storeDoctor", [DoctorController::class, 'storeDoctor'])->middleware('auth:sanctum');
        Route::post("/storeDoctorDate/{uuid}", [DoctorController::class, 'storeDoctorDate'])->middleware('auth:sanctum');
        Route::post("/BookingDoctor", [DoctorController::class, 'BookingDoctor'])->middleware('auth:sanctum');
        Route::get('/Doctor', [DoctorController::class ,'Doctor'])->middleware('auth:sanctum');
        Route::get('/ShowDoctor/{uuid}', [DoctorController::class ,'ShowDoctor'])->middleware('auth:sanctum');
        Route::post('/updateDoctor/{uuid}', [DoctorController::class ,'updateDoctor'])->middleware('auth:sanctum');
        Route::get('/destroyDoctor/{uuid}', [DoctorController::class ,'destroyDoctor'])->middleware('auth:sanctum');
        Route::post("/searchDoctor", [DoctorController::class, 'searchDoctor'])->middleware('auth:sanctum');
    });
    Route::prefix('Employee')->group(function () {
        Route::get('/Employee', [EmployeeController::class ,'Employee'])->middleware('auth:sanctum');
        Route::get('/ShowEmployee/{uuid}', [EmployeeController::class ,'ShowEmployee'])->middleware('auth:sanctum');
        Route::post('/storeEmployee', [EmployeeController::class ,'storeEmployee'])->middleware('auth:sanctum');
         Route::post('/storeEmployeeDate/{uuid}', [EmployeeController::class ,'storeEmployeeDate'])->middleware('auth:sanctum');
        Route::post('/updateEmployee/{uuid}', [EmployeeController::class ,'updateEmployee'])->middleware('auth:sanctum');
        Route::get('/destroyEmployee/{uuid}', [EmployeeController::class ,'destroyEmployee'])->middleware('auth:sanctum');
        Route::post("/searchEmployee", [EmployeeController::class, 'searchEmployee'])->middleware('auth:sanctum');
    });
   Route::prefix('Nurse')->group(function () {
        Route::get('/Nurse', [NurseController::class ,'Nurse'])->middleware('auth:sanctum');
        Route::get('/ShowNurse/{uuid}', [NurseController::class ,'ShowNurse'])->middleware('auth:sanctum');
        Route::post('/storeNurse', [NurseController::class ,'storeNurse'])->middleware('auth:sanctum');
        Route::post('/storeNurseDate/{uuid}', [NurseController::class ,'storeNurseDate'])->middleware('auth:sanctum');
        Route::post('/updateNurse/{uuid}', [NurseController::class ,'updateNurse'])->middleware('auth:sanctum');
        Route::get('/destroyNurse/{uuid}', [NurseController::class ,'destroyNurse'])->middleware('auth:sanctum');
        Route::post("/searchNurse", [NurseController::class, 'searchNurse'])->middleware('auth:sanctum');
    });
       Route::prefix('Patient')->group(function () {
        Route::get('/Patient', [PatientController::class ,'Patient'])->middleware('auth:sanctum');
        Route::get('/ShowPatient/{uuid}', [PatientController::class ,'ShowPatient'])->middleware('auth:sanctum');
        Route::post('/storePatient', [PatientController::class ,'storePatient'])->middleware('auth:sanctum');
        Route::post('/storePatientDate', [PatientController::class ,'storePatientDate'])->middleware('auth:sanctum');
        Route::post('/updatePatient/{uuid}', [PatientController::class ,'updatePatient'])->middleware('auth:sanctum');
        Route::get('/destroyPatient/{uuid}', [PatientController::class ,'destroyPatient'])->middleware('auth:sanctum');
        Route::get('/PatientInformation', [PatientController::class ,'PatientInformation'])->middleware('auth:sanctum');
        Route::post('/storePatientInformation', [PatientController::class ,'storePatientInformation'])->middleware('auth:sanctum');
        Route::post('/updatePatientInformation/{uuid}', [PatientController::class ,'updatePatientInformation'])->middleware('auth:sanctum');
        Route::get('/destroyPatientInformation/{uuid}', [PatientController::class ,'destroyPatientInformation'])->middleware('auth:sanctum');
        Route::post("/searchPatient", [PatientController::class, 'searchPatient'])->middleware('auth:sanctum');
    });
   Route::prefix('Report')->group(function () {
        Route::get('/Report', [ReportController::class ,'Report'])->middleware('auth:sanctum');
        Route::post('/ShowReport', [ReportController::class ,'ShowReport'])->middleware('auth:sanctum');
        Route::post('/storeReport', [ReportController::class ,'storeReport'])->middleware('auth:sanctum');
        Route::post('/updateReport/{uuid}', [ReportController::class ,'updateReport'])->middleware('auth:sanctum');
        Route::get('/destroyReport/{uuid}', [ReportController::class ,'destroyReport'])->middleware('auth:sanctum');
        Route::post("/searchReport", [ReportController::class, 'searchReport'])->middleware('auth:sanctum');
    });
      Route::prefix('Medicine')->group(function () {
        Route::get('/Medicines', [MedicineController::class ,'Medicines'])->middleware('auth:sanctum')->middleware('auth:sanctum');
        Route::get('/getMostRequestedMedicines', [MedicineController::class ,'getMostRequestedMedicines'])->middleware('auth:sanctum');
        Route::post('/storeReport', [MedicineController::class ,'storeReport'])->middleware('auth:sanctum');
        Route::post('/updateReport/{uuid}', [MedicineController::class ,'updateReport'])->middleware('auth:sanctum');
        Route::get('/destroyReport/{uuid}', [MedicineController::class ,'destroyReport'])->middleware('auth:sanctum');
        Route::post("/searchReport", [MedicineController::class, 'searchReport'])->middleware('auth:sanctum');
    });
    Route::prefix('Illness')->group(function () {
     
        Route::post('/storeIllness', [IllnessController::class ,'storeIllness'])->middleware('auth:sanctum');

        Route::get('/destroyIllness/{uuid}', [IllnessController::class ,'destroyReport'])->middleware('auth:sanctum');
        
    });

});