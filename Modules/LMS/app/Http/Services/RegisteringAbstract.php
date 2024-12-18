<?php

namespace Modules\LMS\app\Http\Services;

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
    use CustomerTrait, PsPaymentTrait, OrderTrait, InvoiceTrait;

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

        $enroll = Enroll::create([
            'course_id' => $this -> course -> id,
            'study_completed' => 0,
            'study_count' => 0
        ]);
        $this->enrollID = $enroll->id;

    }

    protected function setStudent()
    {
        $student = Student::create([]);
        $this->studentID = $student->id;
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
                "total_price" => $this->course->price,
            ]);

            PsPaymentStatus::create([
                "status_id" => $this->waitToPayStatus()->id,
                "payment_id" => $psPayment->id,
                "create_date" => now(),
                "creator_id" => $this->user->id
            ]);
        })->pay();

    }
}
