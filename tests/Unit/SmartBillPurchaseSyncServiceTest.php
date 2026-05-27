<?php

namespace Tests\Unit;

use App\Exceptions\SmartBillException;
use App\Mail\BillingTestInvoiceMail;
use App\Models\Report;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillPurchaseSyncService;
use App\Services\SmartBillService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmartBillPurchaseSyncServiceTest extends TestCase
{
    public function test_it_marks_test_purchases_as_completed_after_a_successful_sync(): void
    {
        $report = $this->makeReport([
            'id' => 10,
            'is_test' => true,
        ]);
        $purchase = $this->makePurchase([
            'id' => 20,
            'report_id' => 10,
            'status' => 'paid',
            'metadata' => [],
            'paid_at' => now(),
        ], $report);

        $invoice = (new SmartBillInvoice())->forceFill([
            'id' => 30,
            'status' => 'completed',
            'payment_status' => 'registered',
        ]);

        $smartBill = $this->createMock(SmartBillService::class);
        $smartBill->expects($this->once())
            ->method('syncPurchase')
            ->with($purchase)
            ->willReturn($invoice);

        $service = new SmartBillPurchaseSyncService($smartBill);

        $service->syncPaidPurchase($purchase);

        $this->assertSame('test_completed', $report->updatedPayloads[0]['status'] ?? null);
        $this->assertArrayHasKey('processed_at', $report->updatedPayloads[0] ?? []);
        $this->assertArrayHasKey('error_message', $report->updatedPayloads[0] ?? []);
        $this->assertNull($report->updatedPayloads[0]['error_message']);
    }

    public function test_it_sends_the_test_invoice_email_when_requested(): void
    {
        Mail::fake();

        $report = $this->makeReport([
            'id' => 11,
            'is_test' => true,
            'email' => 'billing-test@example.com',
        ]);
        $purchase = $this->makePurchase([
            'id' => 21,
            'report_id' => 11,
            'status' => 'paid',
            'email' => 'billing-test@example.com',
            'customer_email' => 'billing-test@example.com',
            'metadata' => ['send_test_invoice_email' => '1'],
            'paid_at' => now(),
        ], $report);

        $invoice = (new SmartBillInvoice())->forceFill([
            'id' => 31,
            'status' => 'completed',
            'payment_status' => 'registered',
            'invoice_series' => 'GYC',
            'invoice_number' => '0001',
        ]);

        $smartBill = $this->createMock(SmartBillService::class);
        $smartBill->expects($this->once())
            ->method('syncPurchase')
            ->with($purchase)
            ->willReturn($invoice);

        $service = new SmartBillPurchaseSyncService($smartBill);

        $service->syncPaidPurchase($purchase);

        Mail::assertSent(BillingTestInvoiceMail::class, function (BillingTestInvoiceMail $mail) use ($purchase, $invoice): bool {
            return $mail->purchase === $purchase && $mail->invoice === $invoice;
        });
        $this->assertArrayHasKey('test_invoice_email_sent_at', $purchase->metadata);
        $this->assertSame(1, $purchase->saveCalls);
    }

    public function test_it_does_not_block_normal_report_processing_when_smartbill_sync_fails(): void
    {
        $report = $this->makeReport([
            'id' => 12,
            'is_test' => false,
        ]);
        $purchase = $this->makePurchase([
            'id' => 22,
            'report_id' => 12,
            'status' => 'paid',
            'metadata' => [],
            'paid_at' => now(),
        ], $report);

        $smartBill = $this->createMock(SmartBillService::class);
        $smartBill->expects($this->once())
            ->method('syncPurchase')
            ->with($purchase)
            ->willThrowException(new SmartBillException('Temporary SmartBill error', true));

        $service = new SmartBillPurchaseSyncService($smartBill);

        $service->syncPaidPurchase($purchase);

        $this->assertSame([], $report->updatedPayloads);
    }

    private function makeReport(array $attributes): Report
    {
        $report = new class extends Report {
            public array $updatedPayloads = [];

            public function update(array $attributes = [], array $options = []): bool
            {
                $this->updatedPayloads[] = $attributes;
                $this->forceFill($attributes);

                return true;
            }
        };

        $report->forceFill([
            ...$attributes,
        ]);

        return $report;
    }

    private function makePurchase(array $attributes, Report $report): ReportPurchase
    {
        $purchase = new class extends ReportPurchase {
            public int $saveCalls = 0;

            public function save(array $options = []): bool
            {
                $this->saveCalls++;

                return true;
            }
        };

        $purchase->forceFill([
            ...$attributes,
        ]);
        $purchase->setRelation('report', $report);
        $purchase->setRelation('smartBillInvoice', null);

        return $purchase;
    }
}