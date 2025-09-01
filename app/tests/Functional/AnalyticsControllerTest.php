<?php
namespace App\Tests\Functional;

class AnalyticsControllerTest extends BaseWebTestCase
{
    public function testCampaignStats(): void
    {
        $cid = 'cmp-test';
        $this->client->request('GET', "/api/analytics/$cid");
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('opens', $data);
        $this->assertArrayHasKey('clicks', $data);
        $this->assertArrayHasKey('openRate', $data);
        $this->assertArrayHasKey('clickRate', $data);
    }
}
