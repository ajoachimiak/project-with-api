<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SendMailTest extends WebTestCase
{

    public function testEmailsAreSentCorrectly() {

        $client = static::createClient();

        $client->request('GET', '/email');

        $sentMail = $this->getMailerMessage();

        $this->assertEmailCount(1);
        $this->assertEmailHeaderSame($sentMail, 'To', 'Support <oj5c9mru1@joachimiak-adrian.pl>');
        $this->assertEmailTextBodyContains($sentMail, 'This is a test message');
    }
}