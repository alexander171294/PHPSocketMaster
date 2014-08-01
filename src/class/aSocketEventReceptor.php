<?php

abstract class SocketEventReceptor
{

	abstract private function onError();
	abstract private function onConnect();
	abstract private function onReceiveMessage($message);
}