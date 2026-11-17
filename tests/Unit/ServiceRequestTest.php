<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Models\ServiceRequest;

class ServiceRequestTest extends TestCase
{
    private ServiceRequest $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ServiceRequest();
    }

    public function testGetValidStatusesContainsAllExpectedStatuses(): void
    {
        $statuses = ServiceRequest::getValidStatuses();

        $this->assertContains('pending', $statuses);
        $this->assertContains('assigned', $statuses);
        $this->assertContains('accepted', $statuses);
        $this->assertContains('on_the_way', $statuses);
        $this->assertContains('repairing', $statuses);
        $this->assertContains('completed', $statuses);
        $this->assertContains('cancelled', $statuses);
    }

    public function testValidateFailsWhenRequiredFieldsAreEmpty(): void
    {
        $errors = $this->model->validate([]);

        $this->assertArrayHasKey('customer_id', $errors);
        $this->assertArrayHasKey('service_id', $errors);
        $this->assertArrayHasKey('complaint', $errors);
        $this->assertArrayHasKey('address', $errors);
        $this->assertArrayHasKey('schedule', $errors);
    }

    public function testValidateFailsWhenComplaintOrAddressTooShort(): void
    {
        $errors = $this->model->validate([
            'customer_id' => 1,
            'service_id'  => 2,
            'complaint'   => 'AC', // < 5 karakter
            'address'     => 'Jl', // < 5 karakter
            'schedule'    => '2026-11-20 10:00:00'
        ]);

        $this->assertArrayHasKey('complaint', $errors);
        $this->assertArrayHasKey('address', $errors);
    }

    public function testValidateFailsWhenStatusIsInvalid(): void
    {
        $errors = $this->model->validate([
            'customer_id' => 1,
            'service_id'  => 2,
            'complaint'   => 'AC tidak dingin dan mengeluarkan air',
            'address'     => 'Jl. Sudirman No. 123, Jakarta',
            'schedule'    => '2026-11-20 10:00:00',
            'status'      => 'status_tidak_jelas'
        ]);

        $this->assertArrayHasKey('status', $errors);
    }

    public function testValidatePassesWithValidData(): void
    {
        $errors = $this->model->validate([
            'customer_id' => 1,
            'service_id'  => 2,
            'complaint'   => 'AC tidak dingin dan mengeluarkan air',
            'address'     => 'Jl. Sudirman No. 123, Jakarta',
            'schedule'    => '2026-11-20 10:00:00',
            'status'      => ServiceRequest::STATUS_PENDING
        ]);

        $this->assertCount(0, $errors);
    }
}

