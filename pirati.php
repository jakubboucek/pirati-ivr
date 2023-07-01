<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

const SuperSecretPassword = '101168';

const MessageError = '9';
const MessageWrongPass = '19';
const MessageRightPass = '20';

\Tracy\Debugger::enable(\Tracy\Debugger::Detect, __DIR__ . '/log');

$factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$requestCreator = new \Nyholm\Psr7Server\ServerRequestCreator($factory, $factory, $factory, $factory);
$request = $requestCreator->fromGlobals();
$response = $factory->createResponse();
$sender = new \Lazzard\Psr7ResponseSender\Sender();

try {
    $ivr = Jakubboucek\Odorik\Ivr\Request::fromHttpRequest($request);
    $builder = Jakubboucek\Odorik\Ivr\ResponseBuilder::create();
    $builder->play($ivr->getDtmf() === SuperSecretPassword ? MessageRightPass : MessageWrongPass)->hangUp();
    $sender->send($builder->toHttpResponse($response, $factory));
} catch (\Jakubboucek\Odorik\Ivr\Exception\InvalidRequestException $e) {
    $sender->send(Jakubboucek\Odorik\Ivr\ResponseBuilder::create()->play(MessageError)->hangUp()->toHttpResponse($response, $factory));
}

