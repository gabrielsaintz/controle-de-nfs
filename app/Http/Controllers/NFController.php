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

    /**
     * @OA\Get(
     *     path="/api/nfs",
     *     summary="Lista todas as notas fiscais.",
     *     tags={"NFController"},
     *     @OA\Response(
     *         response="200",
     *         description="Lista de notas fiscais.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/NotaFiscal")
     *         )
     *     )
     * )
     */

    public function index()
    {
        $nfs = $this->apiNFService->fetchData();
        return response()->json($nfs, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @OA\Get(
     *     path="/api/nfs/{cnpj}",
     *     summary="Lista todas as notas fiscais para um CNPJ específico.",
     *     tags={"NFController"},
     *     @OA\Parameter(
     *         name="cnpj",
     *         in="path",
     *         required=true,
     *         description="CNPJ do cliente.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lista de notas fiscais para o CNPJ especificado.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/NotaFiscal")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Não foram encontradas notas fiscais para este CNPJ.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Não foram encontradas notas fiscais para este CNPJ."
     *             )
     *         )
     *     )
     * )
     */

    public function showNotesForCnpj(string $cnpj)
    {
        $nfsCnpj = $this->apiNFService->fetchForCnpj($cnpj);
        $lenght = count($nfsCnpj);

        if ($lenght < 1) {
            return response()->json(['message' => 'Não foram encontradas notas fiscais para este CNPJ.'], 404);
        }

        return response()->json($nfsCnpj, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @OA\Get(
     *     path="/api/nfs/{cnpj}/total",
     *     summary="Retorna o valor total das notas fiscais para um CNPJ específico.",
     *     tags={"NFController"},
     *     @OA\Parameter(
     *         name="cnpj",
     *         in="path",
     *         required=true,
     *         description="CNPJ do cliente.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Valor total das notas fiscais para o CNPJ especificado.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="value",
     *                 type="number",
     *                 example="1234.56"
     *             )
     *         )
     *     )
     * )
     */

    public function showTotalValueOfNotes(string $cnpj)
    {
        $totalValue = $this->apiNFService->fetchTotalValueOfNotes($cnpj);
        return response()->json($totalValue, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/nfs/{cnpj}/valor-comprovado",
     *     summary="Obtém o valor total das notas fiscais confirmadas para um CNPJ específico.",
     *     tags={"NFController"},
     *     @OA\Parameter(
     *         name="cnpj",
     *         in="path",
     *         required=true,
     *         description="CNPJ do cliente.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Valor total das notas fiscais confirmadas.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valor", type="number", example="1000.00")
     *         )
     *     )
     * )
     */

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
