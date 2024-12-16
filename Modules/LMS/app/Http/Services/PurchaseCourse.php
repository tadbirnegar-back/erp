<?php
namespace Modules\LMS\App\Http\Services;

class PurchaseCourse extends RegisteringAbstract
{
    protected $data = [];
    public function __construct($course , $user){
        $this -> course = $course;
        $this -> user = $user;
    }

    public function handle()
    {
        $this->getCustomer();
        $this->storeEnroll();
        $this->getOrder();
        $this->getInvoice();

        //ChargeAble Part
        if($this -> course -> price > 0){
            $info =  $this -> chargeable();
            $data = [
                "type" => "chargeable",
                "info" => $info,
            ];
            return $data;
        }
        $data = [
            "type" => "free" ,
            "info" => "Registered"
        ];
        return $data;
    }

    private function chargeable(){
        $this -> getZarinpalInvoice();
        return $this->finalPaymentMethod();
    }


}
