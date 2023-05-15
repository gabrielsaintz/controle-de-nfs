<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiNFService;
use GuzzleHttp\Client;

class NFController extends Controller
{
    protected $ApiNFService;

    

    public function __construct(ApiNFService $apiNFService)
    {
        $this->apiNFService = $apiNFService;
    }

    public function index()
    {
        $nfs = $this->apiNFService->fetchData();
        return response()->json($nfs, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function showNotesForCnpj(string $cnpj)
    {
        $nfsCnpj = $this->apiNFService->fetchForCnpj($cnpj);
        $lenght = count($nfsCnpj);

        if ($lenght < 1) {
            return response()->json(['message' => 'Não foram encontradas notas fiscais para este CNPJ.'], 404);
        }

        return response()->json($nfsCnpj, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function showTotalValueOfNotes(string $cnpj)
    {
        $totalValue = $this->apiNFService->fetchTotalValueOfNotes($cnpj);
        return response()->json($totalValue, 200);
    }

    public function showValueOfComfirmedNotes(string $cnpj)
    {
        $value = $this->apiNFService->fetchValueOfNotesDelivered($cnpj);
        return response()->json(["valor"=>$value], 200);
    }

    public function showValueOfOpenNotes(string $cnpj)
    {
        $value = $this->apiNFService->fetchValueOfOpenNotes($cnpj);
        return response()->json(["valor"=>$value], 200);
    }

    public function showLostValueDueToDelay(string $cnpj)
    {
        $value = $this->apiNFService->fetchLostValueDueToDelay($cnpj);
        return response()->json(["valor"=>$value], 200);
    }

}
