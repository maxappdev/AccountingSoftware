<?php

namespace Modules\Barcodes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Barcodes\Entities\Barcode;

class BarcodesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateData ($request);
        $barcode = Barcode::create ($data);
        return response ()->json ($barcode);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function generate(Request $request)
    {
        $data = $this->validateGenerateData ($request);
        $lastBarcode = Barcode::orderBy('id', 'DESC')->first();
        if(!$lastBarcode) {
            $number = 1;
        } else {
            $number = $lastBarcode->id + 1;
        }

        if($data['format'] === 'ean13'){
            $code = $this->generateEAN($number);
        }
        if($data['format'] === 'code128'){
            $code = $this->generateCode128($number);
        }
        if($data['format'] === 'code39'){
            $code = $this->generateCode128($number);
        }
        if($data['format'] === 'upca'){
            $code = $this->generateUPCA(12345678912);
        }

        if(Barcode::where ('value', $code)->exists()){
            if($data['format'] === 'ean13'){
                $code = $this->generateEAN($number);
            }
            if($data['format'] === 'code128'){
                $code = $this->generateCode128($number);
            }
            if($data['format'] === 'code39'){
                $code = $this->generateCode128($number);
            }
            if($data['format'] === 'upca'){
                $code = $this->generateUPCA(12345678912);
            }
            return response ()->json ($code);
        } else {
            return response ()->json ($code);
        }
    }

    private function validateData(Request $request) {
        return $request->validate ([
            "value" => "required|unique:barcodes, value",
            "format" => "required",
            "barcodeUrl" => "required|active_url",
        ]);
    }

    private function validateGenerateData(Request $request) {
        return $request->validate ([
            "format" => "required",
        ]);
    }

    private function generateEAN($number) {
        $code = '200' . str_pad($number, 9, '0');
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
        // loop backwards to make the loop length-agnostic. The same basic functionality
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--)
        {
            $sum += (int)$code[$i] * ($weightflag?3:1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    private function generateCode128($number) {
        $code = '200';
        $weightflag = true;
        $sum = 0;
        // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
        // loop backwards to make the loop length-agnostic. The same basic functionality
        // will work for codes of different lengths.
        for ($i = strlen($code) - 1; $i >= 0; $i--)
        {
            $sum += (int)$code[$i] * ($weightflag?3:1);
            $weightflag = !$weightflag;
        }
        $code .= (10 - ($sum % 10)) % 10;
        return $code;
    }

    private function generateUPCA(int $upc_code)
    {
        $checkDigit = -1; // -1 == failure
        $upc = substr($upc_code,0,11);
        // send in a 11 or 12 digit upc code only
        if (strlen($upc) == 11 && strlen($upc_code) <= 12) {
            $oddPositions = $upc[0] + $upc[2] + $upc[4] + $upc[6] + $upc[8] + $upc[10];
            $oddPositions *= 3;
            $evenPositions= $upc[1] + $upc[3] + $upc[5] + $upc[7] + $upc[9];
            $sumEvenOdd = $oddPositions + $evenPositions;
            $checkDigit = (10 - ($sumEvenOdd % 10)) % 10;
        }
        return $checkDigit;
    }
}