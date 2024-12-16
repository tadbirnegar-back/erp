<?php

namespace Modules\LMS\App\Http\Services;

use Illuminate\Support\Facades\Log;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\app\Http\Traits\CustomerTrait;
use Modules\CustomerMS\app\Models\Customer;
use Modules\Gateway\app\Models\Payment as PG;
use Modules\LMS\app\Models\Course;
use Modules\LMS\app\Models\Enroll;
use Modules\LMS\app\Models\Student;
use Modules\PayStream\app\Http\Traits\InvoiceTrait;
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Http\Traits\PsPaymentTrait;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\Invoice;
use Modules\PayStream\app\Models\InvoiceStatus;
use Modules\PayStream\app\Models\Online;
use Modules\PayStream\app\Models\Order;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PayStream\app\Models\PsPayments;
use Modules\PayStream\app\Models\PsPaymentStatus;
use Shetabit\Payment\Facade\Payment;

abstract class RegisteringAbstract
{
    use CustomerTrait, PsPaymentTrait , OrderTrait , InvoiceTrait;

    protected Course $course;
    protected User $user;
    protected int $enrollID;
    protected int $studentID;
    protected int $customerID;

    protected Order $order;

    protected Invoice $invoice;
    protected Online $online;
    protected \Shetabit\Multipay\Invoice $zarinpalInvoice;

    protected function getCourseID()
    {
        return $this->course;
    }

    protected function setEnrollID()
    {
        $courseID = $this->getCourseID()->id;
        $enroll = Enroll::where('course_id', $courseID)->select('id')->first();
        if ($enroll) {
            $this->enrollID = $enroll->id;
        } else {
            $enroll = Enroll::create([
                'course_id' => $courseID,
                'study_completed' => 0,
                'study_count' => 0
            ]);
            $this->enrollID = $enroll->id;
        }
        return $this->enrollID;
    }

    protected function getEnrollID(): int
    {
        return $this->enrollID ?? $this->setEnrollID();
    }

    protected function storeEnroll()
    {
        $this->getEnrollID();
    }


    protected function setStudent()
    {
        $student = Student::create([]);
        $this->studentID = $student->id;
        Log::info("hi");

    }

    protected function getCustomer()
    {
        $customer = Customer::where('person_id', $this->user->person_id)
            ->whereMorphedTo('customerable', Student::class)
            ->first();
        if ($customer) {
            $this->customerID = $customer->id;
            $this->studentID = $customer->customerable_id;
        } else {
            $this->setStudent();
            $this->setCustomer();
        }
    }

    protected function setCustomer()
    {
        $customerStatus = $this->activeCustomerStatus()->id;
        $customer = Customer::create([
            "creator_id" => $this->user->id,
            "person_id" => $this->user->person_id,
            "status_id" => $customerStatus,
            "create_date" => now(),
            "customerable_id" => $this->studentID,
            "customerable_type" => Student::class,
            "customer_type_id" => null
        ]);
        $this->customerID = $customer->id;
    }

    protected function setOrder()
    {
        $order = Order::create([
            "create_date" => now(),
            "customer_id" => $this->customerID,
            "creator_id" => $this->user->id,
            "description" => "Enrolling User to course",
            "orderable_type" => Enroll::class,
            "orderable_id" => $this->enrollID,
            "requested_invoice_count" => 1,
            "total_price" => $this->course->price
        ]);
        $this->order = $order;

    }


    protected function getOrder()
    {
        $order = Order::where("customer_id", $this->customerID)->where("orderable_type", Enroll::class)->where("orderable_id", $this->enrollID)->first();

        if ($order) {
            $this->order = $order;
        } else {
            $this->setOrder();
        }

        if($this -> course -> price > 0){

        }else{
            ProcessStatus::create([
                "order_id" => $this->order->id,
                "status_id" => $this->orderProcRegistered()->id,
                "creator_id" => $this->user->id
            ]);

            FinancialStatus::create([
                "order_id" => $this->order->id,
                "status_id" => $this->orderFinPardakhtShode()->id,
                "creator_id" => $this->user->id
            ]);
        }

    }


    protected function setInvoice()
    {
        $invoice = Invoice::create([
            "creator_id" => $this->user->id,
            "due_date" => now(),
            "create_date" => now(),
            "order_id" => $this->order->id,
            "total_price" => $this->order->total_price
        ]);

        $this->invoice = $invoice;
    }

    protected function getInvoice()
    {
        $invoice = Invoice::where('order_id', $this->order->id)->first();
        if ($invoice) {
            $this->invoice = $invoice;
        } else {
            $this->setInvoice();
        }
        if($this->course->price > 0){
            InvoiceStatus::create([
                "invoice_id" => $this->invoice->id,
                "status_id" => $this->waitToPayInvoiceStatus()->id,
            ]);
        }else{
            InvoiceStatus::create([
                "invoice_id" => $this->invoice->id,
                "status_id" => $this->payedInvoiceStatus()->id,
            ]);
        }

    }


    protected function setZarinpalInvoice(): void
    {
        $invoice = (new \Shetabit\Multipay\Invoice)->amount($this->course->price);
        $this->zarinpalInvoice = $invoice;
    }

    protected function getZarinpalInvoice()
    {
        $this->setZarinpalInvoice();
    }


    protected function finalPaymentMethod()
    {
        return Payment::via('zarinpal')->purchase($this->zarinpalInvoice, function ($driver, $transactionId) {

            $online = Online::create([
                "authority" => $transactionId,
            ]);

            $psPayment = PsPayments::create([
                "ps_paymentable_type" => Online::class,
                "ps_paymentable_id" => $online->id,
                "creator_id" => $this->user->id,
                "create_date" => now(),
                "invoice_id" => $this->invoice->id,
                "payment_date" => now(),
                "total_price" => $this->course->id,
            ]);

            PsPaymentStatus::created([
                "status_id" => $this -> waitToPayStatus() -> id,
                "payment_id" => $psPayment -> id ,
                "create_date" => now(),
                "creator_id" => $this -> user -> id
            ]);
        })->pay();

    }
}
