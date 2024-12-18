<?php
namespace Modules\LMS\App\Http\Services;

use Modules\PayStream\app\Models\Online;
use Modules\PayStream\app\Models\PsPayments;
use Modules\PayStream\app\Models\PsPaymentStatus;
use Shetabit\Payment\Facade\Payment;

class PurchaseCourse extends RegisteringAbstract
{
    protected $data = [];
    protected static Online $onlineAfterPayment;
    public function __construct($course , $user){
        $this -> course = $course;
        $this -> user = $user;
    }

    public function handle()
    {
        $this->setStudent();
        $this->setCustomer();
        $this->setEnrollID();
        $this->setOrder();
        $this->setInvoice();

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


    public static function setPaymentStatusAfterPayment(Online $online)
    {
        self::$onlineAfterPayment = $online;
        $online -> load('psPayments');
        $payment = PsPayments::find($online->psPayments[0]->id);
        //Check Payment
        $total = $payment->total_price;
        $receipt = Payment::amount($total)->transactionId($online->authority)->verify();

        $transactionid = $receipt->getReferenceId();

        //Change Payment Status
        PsPaymentStatus::create([
            "status_id" => self::Statuses()["payment"],
            "payment_id" => $payment->id,
            "create_date" => now(),
            "creator_id" => $payment -> creator_id,
        ]);
    }

    private function Statuses(){
        return [
            "payment" => $this->payedStatus()->id,
        ];
    }

}
