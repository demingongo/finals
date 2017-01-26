<?php

namespace Novice\Test;

class NewsletterManager
{
    private $mailer;

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

	public function getMailer()
    {
        return $this->mailer;
    }

    // ...
}