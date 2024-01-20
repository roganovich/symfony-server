<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Conferences');
    }

    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('View');

        $this->assertPageTitleContains('Paris');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Paris 2020');
        $this->assertSelectorExists('div:contains("There are 1/1 comments")');
    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conferences/amsterdam-2019');

        #dd(dirname(__DIR__, 3).'/public/uploads/noimage.png');
        $client->submitForm('Submit', [
            'comment[author]' => 'Roman',
            'comment[text]' => 'Some feedback from an automated functional test',
            'comment[email]' => 'roman@test.te',
            'comment[photo]' => dirname(__DIR__, 3).'/public/uploads/noimage.png',
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2/2 comments")');
    }
}