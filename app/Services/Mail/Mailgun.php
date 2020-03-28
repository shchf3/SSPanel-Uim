<?php

namespace App\Services\Mail;

use App\Services\Config;
use Mailgun\Mailgun as MailgunService;
use Mailgun\HttpClientConfigurator;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class Mailgun extends Base
{
    private $config;
    private $mg;
    private $domain;
    private $sender;
    private $client_config;

    public function __construct()
    {
        $this->config = $this->getConfig();

        $client_config = [
            'proxy' => '127.0.0.1:1080',
        ];
        $adapter = GuzzleAdapter::createWithConfig($client_config);
        $configurator = new HttpClientConfigurator();
        $configurator->setApiKey($this->config['key']);
        $configurator->setEndpoint('https://api.mailgun.net');
        $configurator->setHttpClient($adapter);

        $this->mg = MailgunService::configure($configurator);
        $this->domain = $this->config['domain'];
        $this->sender = $this->config['sender'];
    }

    public function getConfig()
    {
        return [
            'key' => Config::get('mailgun_key'),
            'domain' => Config::get('mailgun_domain'),
            'sender' => Config::get('mailgun_sender')
        ];
    }

    public function send($to, $subject, $text, $files)
    {
        $inline = array();
        foreach ($files as $file) {
            $inline[] = array('filePath' => $file, 'filename' => basename($file));
        }
        if (count($inline) == 0) {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text
                ]);
        } else {
            $this->mg->messages()->send($this->domain, [
                    'from' => $this->sender,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $text,
                    'inline' => $inline
                ]);
        }
    }
}
