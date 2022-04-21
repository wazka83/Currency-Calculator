<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ApiKeyType;
use App\Form\ConverterType;
use App\Form\CheckGrowthType;
use App\Entity\CurrencyManager;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(Request $request): Response
    {
        
        $manager=new CurrencyManager('1535a8ffed9a203d2b92a107a95d4ffa');
        $convertResult=['code1'=>null, 'code2'=>null,'date'=>null, 'result'=>null, 'value'=>null];
        $equal=null;
        $checkResult=[
        'date1'=>null,
        'date2'=>null, 
        'code1'=>null,
        'code2'=>null,
        'result1'=>null,
        'result2'=>null,
        'percent'=>null,
        'isGrowing'=>null
        ];
        $apiForm=$this->createForm(ApiKeyType::class, null, [
                'method'=>'POST',
        ]);
        //handleRequest only processes data when this is POST method
        $apiForm->handleRequest($request);
        
        if($apiForm->isSubmitted()&&$apiForm->isValid()){
           dump($apiForm->getData());
           $apiKey=($apiForm->getData())['apiKey'];
           dump($apiKey);
        };
        
      
        $converterForm=$this->createForm(ConverterType::class, null, [
        ]);
        $converterForm->handleRequest($request);
        
        if($converterForm->isSubmitted() && $converterForm->isValid()){
            $converterData=($converterForm->getData());
            $manager->UpdateLatest();
            $convertResult=$manager->Convert($converterData['value'], $converterData['first_currency'], $converterData['second_currency']);
            $equal="=";
         };
         
        $checkerForm=$this->createForm(CheckGrowthType::class, null, [
        ]);
        
        $checkerForm->handleRequest($request);
        
        if($checkerForm->isSubmitted() && $checkerForm->isValid()){
            $checkerData=($checkerForm->getData());
            dump($checkerData);
            $checkResult=$manager->CompareCurrency($checkerData['firstDate'],$checkerData['secondDate'], $checkerData['first_currency'], $checkerData['second_currency']);
         };

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'title' => 'Currency Calculator',
            'ApiKeyType' => $apiForm->createView(),
            'ConverterType'=>$converterForm->createView(),
            'CheckGrowthType' => $checkerForm->createView(),
            'latestCode1' =>  $convertResult['code1'],
            'latestCode2' => $convertResult['code2'],
            'latestDate' => $convertResult['date'],
            'latestResult'=>$convertResult['result'],
            'latestValue'=>$convertResult['value'],
             'pastDate1'=>$checkResult['date1'],
             'pastDate2'=>$checkResult['date2'],
             'pastCode1'=>$checkResult['code1'],
             'pastCode2'=>$checkResult['code2'],
             'pastResult1'=>$checkResult['result1'],
             'pastResult2'=>$checkResult['result2'],
             'percent'=>$checkResult['percent'],
             'equal'=>$equal,
        ]);
    }

}
