<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;

class ApiNFService
{

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://homologacao3.azapfy.com.br',
        ]);
    }

    public function fetchData()
    {
        $response = $this->client->get('/api/ps/notas');
        $notas = json_decode($response->getBody());
        return $notas;
    }

    public function fetchForCnpj(string $cnpj)
    {
        $todasNotasFiscais = $this->fetchData();

        $notasFiscaisFiltradas = array_values(array_filter($todasNotasFiscais, function ($notaFiscal) use ($cnpj) {
            return $notaFiscal->cnpj_remete === $cnpj;
        }));

        return $notasFiscaisFiltradas;
    }


    public function fetchValueOfNotesDelivered(string $cnpj)
    {
        $notasFiscaisFiltradas = $this->fetchForCnpj($cnpj);

        $valorTotalComprovado = 0;

        foreach ($notasFiscaisFiltradas as $notaFiscal) 
        {
          if ($notaFiscal->status === "COMPROVADO") 
          {   
                $valor = floatval($notaFiscal->valor);
                $valorTotalComprovado += $valor;                         
          }
        }

        return number_format($valorTotalComprovado, 2, '.', ',');
    }

    public function fetchValueOfOpenNotes(string $cnpj)
    {
        $notasFiscaisFiltradas = $this->fetchForCnpj($cnpj);

        $valorTotalEmAberto = 0;

        foreach ($notasFiscaisFiltradas as $notaFiscal) {
          if ($notaFiscal->status === "ABERTO" ) 
          {   
                $valor = floatval($notaFiscal->valor);
                $valorTotalEmAberto += $valor;                              
          }
        }

        return number_format($valorTotalEmAberto, 2, '.', ',');
    }

    public function fetchLostValueDueToDelay(string $cnpj)
    {
        $notasFiscaisFiltradas = $this->fetchForCnpj($cnpj);

        $valorTotalPerdidoPorAtraso = 0;

        foreach ($notasFiscaisFiltradas as $notaFiscal) {
            if ($notaFiscal->status === "COMPROVADO") 
            {              
              $dataEmis = Carbon::createFromFormat('d/m/Y H:i:s', $notaFiscal->dt_emis);
              $dataEntrega = Carbon::createFromFormat('d/m/Y H:i:s', $notaFiscal->dt_entrega);
              $dias = $dataEmis->diffInSeconds($dataEntrega);
                           
              if($dias > 172800) 
              {
                  $valor = floatval($notaFiscal->valor);
                  $valorTotalPerdidoPorAtraso += $valor;  
              }                          
            }
          }

        return number_format($valorTotalPerdidoPorAtraso, 2, '.', ',');
    }
    
    public function fetchTotalValueOfNotes(string $cnpj)
    {
        $notasFiscaisFiltradas = $this->fetchForCnpj($cnpj);
   
        $valorTotalComprovado = 0;
        $valorTotalEmAberto = 0;
        $valorTotalPerdidoPorAtraso = 0;

        foreach ($notasFiscaisFiltradas as $notaFiscal) 
        {

            $valor = floatval($notaFiscal->valor);

            if ($notaFiscal->status === "ABERTO" ) 
            {     
                $valorTotalEmAberto += $valor;                              
            } 
            elseif ($notaFiscal->status === "COMPROVADO") 
            {
                $dataEmis = Carbon::createFromFormat('d/m/Y H:i:s', $notaFiscal->dt_emis);
                $dataEntrega = Carbon::createFromFormat('d/m/Y H:i:s', $notaFiscal->dt_entrega);
                $dias = $dataEmis->diffInSeconds($dataEntrega);    
                
                $valorTotalComprovado += $valor; 

                if($dias > 172800) 
                {        
                    $valorTotalPerdidoPorAtraso += $valor;  
                } 
            }
        }

        $relatorio = (object) [
            'totalAReceber' => number_format(floatval($valorTotalComprovado) + floatval($valorTotalEmAberto) - floatval($valorTotalPerdidoPorAtraso), 2, '.', ','),
            'totalComprovado' => number_format($valorTotalComprovado, 2, '.', ','),
            'totalEmAberto' => number_format($valorTotalEmAberto, 2, '.', ','),
            'totalPerdidoPorAtraso' => number_format($valorTotalPerdidoPorAtraso, 2, '.', ',')
        ];

        return $relatorio;
    }
}
