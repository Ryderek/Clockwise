<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GusApi\GusApi;
use GusApi\ReportTypes;
use GusApi\BulkReportTypes;
use GusApi\Exception\NotFoundException;
use GusApi\Exception\InvalidUserKeyException;

class GusApiController extends Controller
{
    private $gus;

    public function __construct(){
        $this->gus = new GusApi(env('APP_GUS_API'));
    }

    public function getNipData(Request $request){
        try {
            $nipToCheck = $request->input('nip');
            if(strlen($nipToCheck) != 0){
                $this->gus->login();
        
                $gusReports = $this->gus->getByNip($nipToCheck);
            
                /*
                var_dump($this->gus->dataStatus());
                var_dump($this->gus->getBulkReport(
                    new \DateTimeImmutable('2019-05-31'),
                    BulkReportTypes::REPORT_DELETED_LOCAL_UNITS
                ));
                foreach ($gusReports as $gusReport) {
                    //you can change report type to other one
                    $reportType = ReportTypes::REPORT_PERSON;
    
                    echo $gusReport->getName();
                    echo 'Address: ' . $gusReport->getStreet() . ' ' . $gusReport->getPropertyNumber() . '/' . $gusReport->getApartmentNumber();
            
                    $fullReport = $this->gus->getFullReport($gusReport, $reportType);
                    var_dump($fullReport);
                }
                */
                
                $gusReport = $gusReports[0];
                $complexAddress = $gusReport->getStreet()." ".$gusReport->getApartmentNumber();
                if(strlen($gusReport->getPropertyNumber()) != 0){
                    $complexAddress .= "/".$gusReport->getPropertyNumber();
                }
                $output = [
                    "customerName" => $gusReport->getName(),
                    "customerTaxIdentityNumber" => $nipToCheck,
                    "customerCountry" => "Polska",
                    "customerPostal" => $gusReport->getZipCode(),
                    "customerCity" => $gusReport->getCity(),
                    "customerAddress" => $complexAddress,
                    "customerDeliveryCountry" => "Polska",
                    "customerDeliveryPostal" => $gusReport->getZipCode(),
                    "customerDeliveryCity" => $gusReport->getCity(),
                    "customerDeliveryAddress" => $complexAddress,
                ];
                return json_encode(["status" => "success", "errorMessage" => "", "output" => $output]);
            }else{
                return json_encode(["status" => "failure", "errorMessage" => "Błąd: Wprowadź poprawny NIP"]);
            }
            
        } catch (InvalidUserKeyException $e) {
            return json_encode(["status" => "critical", "errorMessage" => "Błąd: Niepoprawny klucz API"]);
        } catch (NotFoundException $e) {
            return json_encode(["status" => "failure", "errorMessage" => "Błąd: Nie znaleziono wyników z tym numerem NIP"]);
            /*
            echo sprintf(
                "StatusSesji:%s\nKomunikatKod:%s\nKomunikatTresc:%s\n",
                $this->gus->getSessionStatus(),
                $this->gus->getMessageCode(),
                $this->gus->getMessage()
            );
            */
        }
    }
}
