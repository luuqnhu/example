<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 26/11/2016
 * Time: 10:44 SA
 */

namespace API1Bundle\Logic;

use API1Bundle\Repository\AccidentRepository;
use API1Bundle\Repository\ReportAccidentRepository;
use API1Bundle\Reference\Reference;
use Symfony\Component\Config\Definition\Exception\Exception;


class ReportAccidentLogic {

    private $reportAccidentRepository;
    private $accidentRepository;

    function __construct($dynamodb)
    {
        $this->reportAccidentRepository = new ReportAccidentRepository($dynamodb);
        $this->accidentRepository = new AccidentRepository($dynamodb);
    }

    //get tai nan giao thong theo id
    public function getReportAccidentById($id) {

        $reponse = $this->reportAccidentRepository->getReportAccidentById($id);
        return $reponse;

    }

    // get tai nan giao thong theo toa do va trang thai
    public function getReportAccidentByCoordinate( $status, $latitude, $longitude) {
        $reponse = $this->reportAccidentRepository->getReportAccidentByCoordinate($status, $latitude, $longitude);
        return $reponse;//->get('Items');
    }

    // them thong tin tai nan giao thong
    public function insertReportAccident($username, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $licenseplate, $level) {

        //Lay ma id
        $id = uniqid();
        //Them report vao table report_accident
        $reponse = $this->reportAccidentRepository->insertReportAccident($id, $username, $latitude, $longitude, $timestart, $status,
                                                                        $description, $image, $licenseplate, $level);

        //neu qua trinh thuc hien insert bi loi thi tra ket ve                                                                    $description, $image, $licenseplate, $level);
        if($reponse === FALSE) {
            return $reponse;
        }
        else {  // thưc hien them thong tin vao table accident
            $id_accident = uniqid();
            $reponse = $this->accidentRepository->insertAccident($id_accident, $latitude, $longitude, $timestart, $status,
                $description, $image, $licenseplate, $level);
            if($reponse === FALSE) {
                return $reponse;
            }
            else { // update them thuoc tinh id_accident cho bang report_accident
                $reponse = $this->reportAccidentRepository->updateReportAccident($id, $id_accident);
                return $reponse;
            }

        }
    }

    //comfirm accident
    public function comfirmAccident($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_accident) {

        $id = uniqid();
        $resultInsert = $this->reportAccidentRepository->comfirmAccident($username, $latitude, $longitude, $agree, $disagree,
                                                                         $status, $time, $id_accident, $id);
        if($resultInsert === FALSE)
            return FALSE;
        $resultAcc = $this->accidentRepository->getAccidentById($id_accident);
        try {
            $agree = $agree + $resultAcc->get('Item')['agree']['N'];
            $disagree = $disagree + $resultAcc->get('Item')['disagree']['N'];
            $reponse = $this->accidentRepository->updateAccidentByComfirm($id_accident, $agree, $disagree);
            return $reponse;
        }
        catch (Exception $e){
            $this->reportAccidentRepository->delete($id);
            return FALSE;
        }

    }

    //report accident handled
    public function reportAccidentHandled($username, $latitude, $longitude, $status, $time, $id_accident) {

        $id = uniqid();
        $resultInsert = $this->reportAccidentRepository->insertReportAccidentHandled($id, $username, $latitude, $longitude,
                                                                                        $time, $status, $id_accident);
        if($resultInsert === FALSE)
            return FALSE;

        $response = $this->accidentRepository->updateAccidentHandled($id_accident, $status);
        if($response === FALSE) {
            $this->reportAccidentRepository->delete($id);
            return FALSE;
        }
        return $response;
    }
}