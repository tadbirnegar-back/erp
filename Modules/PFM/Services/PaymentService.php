<?php

namespace Modules\PFM\Services;

use Carbon\Carbon;
use Modules\AAA\app\Models\User;
use Modules\CustomerMS\app\Http\Traits\CustomerTrait;
use Modules\CustomerMS\app\Models\Customer;
use Modules\PayStream\app\Http\Enums\DiscountTypeEnum;
use Modules\PayStream\app\Http\Traits\InvoiceTrait;
use Modules\PayStream\app\Http\Traits\OrderTrait;
use Modules\PayStream\app\Models\Discount;
use Modules\PayStream\app\Models\DiscountInvoice;
use Modules\PayStream\app\Models\FinancialStatus;
use Modules\PayStream\app\Models\Invoice;
use Modules\PayStream\app\Models\InvoiceStatus;
use Modules\PayStream\app\Models\Order;
use Modules\PayStream\app\Models\ProcessStatus;
use Modules\PersonMS\app\Models\Person;
use Modules\PFM\app\Models\Bill;
use Modules\PFM\app\Models\BilledPerson;

class PaymentService
{
    use CustomerTrait , OrderTrait , InvoiceTrait;
    public int $billID;
    public Person $person;
    public Customer $customer;
    public int $price;
    public Order $order;
    public Invoice $invoice;
    public User $user;
    public int $discountAmount;
    public int $maxDays;
    public function __construct($billID , $user , $price , $maxDays , $discountAmount , $person)
    {
        $this->billID = $billID;
        $this->person = $person;
        $this->discountAmount = $discountAmount;
        $this->maxDays = $maxDays;
        $this->price = $price;
        $this->user = $user;
    }

    public function makeUserCustomer()
    {
        $personID = $this->person->id;
        $customer = Customer::where('person_id', $personID)->where('customerable_type' , BilledPerson::class)->first();
        if($customer){
            $this->customer = $customer;
        }else{
            $billedPerson = BilledPerson::create([]);
            $activeCustomerStatus = $this->activeCustomerStatus();
            $customer = Customer::create([
                'person_id' => $personID,
                'customerable_type' => BilledPerson::class,
                'customerable_id' => $billedPerson->id,
                'creator_id' => $this->person->id,
                'status_id' => $activeCustomerStatus->id,
                'create_date' =>now(),
            ]);
            $this->customer = $customer;
        }
    }
    public function generateBill()
    {
        $this->makeOrder();
        $this->makeInvoice();
        $this->setDiscount();
    }


    private function makeOrder()
    {
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'creator_id' => $this->user->id,
            'create_date' => now(),
            'description' => 'Payment for PFM',
            'orderable_type' =>Bill::class,
            'orderable_id' => $this->billID,
            'requested_invoice_count' => 1,
            'total_price' => $this->price,
        ]);

        $waitToMaliStatus = $this->orderProcWaitMali();
        $waitToPayStatus = $this->orderFinWaitPardakht();

        ProcessStatus::create([
            'order_id' => $order->id,
            'creator_id' => $this->user->id,
            'created_date' => now(),
            'status_id' => $waitToMaliStatus->id,
        ]);

        FinancialStatus::create([
            'order_id' => $order->id,
            'creator_id' => $this->user->id,
            'created_date' => now(),
            'status_id' => $waitToPayStatus->id,
        ]);

        $this->order = $order;
    }

    private function makeInvoice()
    {
        $invoice = Invoice::create([
            'order_id' => $this->order->id,
            'creator_id' => $this->user->id,
            'create_date' => now(),
            'due_date' => Carbon::now()->addDays($this->maxDays),
            'total_price' => $this->price,
        ]);
        $waitToPay = $this->waitToPayInvoiceStatus();
        InvoiceStatus::create([
            'invoice_id' => $invoice->id,
            'creator_id' => $this->user->id,
            'created_date' => now(),
            'status_id' => $waitToPay->id,
        ]);

        $this->invoice = $invoice;
    }

    private function setDiscount()
    {
        $discount = Discount::create([
            'max_usage' => null ,
            'order_type' => Bill::class,
            'expired_date' => Carbon::now()->addDays($this->maxDays),
            'title' => 'تخفیف عوارض',
            'value' => $this->discountAmount,
            'value_type' => DiscountTypeEnum::PERCENT->value,
            'created_date' => now(),
        ]);

        DiscountInvoice::create([
            'discount_id' => $discount->id,
            'invoice_id' => $this->invoice->id,
            'created_date' => now(),
        ]);
    }

}
