<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $this->assertSelectorExists('div:contains("No comments have been posted yet for this conference")');
    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conferences/amsterdam-2019');

        #dd(dirname(__DIR__, 3).'/public/uploads/noimage.png');
        $client->submitForm('Submit', [
            'comment[author]' => 'Roman',
            'comment[text]' => 'Some feedback from an automated functional test',
            'comment[email]' => $email = 'test_submit@test.te',
            'comment[photo]' => dirname(__DIR__, 3).'/public/uploads/noimage.png',
        ]);
        $this->assertResponseRedirects();

        // simulate comment validation
        $comment = self::getContainer()->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState(Comment::STATE_PUBLISHED);
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2/2 comments")');
    }
}