<?php
// Basic events
\Monkbot\Framework\Registration::registerEvent('connect_finished', [\Monkbot\Core\Event\Connect::class, 'connect']);
\Monkbot\Framework\Registration::registerEvent('connect_finished', [\Monkbot\Core\Event\PingChecker::class, 'check']);
\Monkbot\Framework\Registration::registerEvent('PONG', [\Monkbot\Core\Event\PingChecker::class, 'checkCookie']);

// Events for users
\Monkbot\Framework\Registration::registerEvent('NICK', [\Monkbot\Core\Event\NickChange::class, 'execute']);
\Monkbot\Framework\Registration::registerEvent('JOIN', [\Monkbot\Core\Event\JoinHandler::class, 'execute']);
\Monkbot\Framework\Registration::registerEvent('352', [\Monkbot\Core\Event\JoinHandler::class, 'parseWho']);

// PRIVMSG commands
\Monkbot\Framework\Registration::registerEvent('PRIVMSG', [\Monkbot\Core\Event\Privmsg\Login::class, 'execute']);
\Monkbot\Framework\Registration::registerEvent('PRIVMSG', [\Monkbot\Core\Event\Privmsg\Register::class, 'execute']);
\Monkbot\Framework\Registration::registerEvent('PRIVMSG', [\Monkbot\Core\Event\Privmsg\Whoami::class, 'execute']);
\Monkbot\Framework\Registration::registerEvent('PRIVMSG', [\Monkbot\Core\Event\Privmsg\Set::class, 'execute']);