<?php

namespace App;

use CLI\Agent;
use CLI\WS;

/**
 * The WebSocket example server utilizing Fat-Free Framework's `\CLI\WS`.
 */
class WebSocketServer
{
    /**
     * @param WS $ws
     */
    public function __construct(WS $ws)
    {
        $this->register($ws);
    }

    /**
     * Registers the event listeners.
     *
     * @param WS $ws
     */
    private function register(WS $ws)
    {
        $ws
            ->on('start', [$this, 'onStart'])
            ->on('error', [$this, 'onError'])
            ->on('stop', [$this, 'onStop'])
            ->on('connect', [$this, 'onConnect'])
            ->on('disconnect', [$this, 'onDisconnect'])
            ->on('idle', [$this, 'onIdle'])
            ->on('receive', [$this, 'onReceive'])
            ->on('send', [$this, 'onSend']);
    }

    /**
     * Sends the provided message to all connected clients of the given server.
     *
     * @param WS $ws
     * @param string $message
     */
    private function sendMessageToAllClients(WS $ws, string $message)
    {
        /** @var Agent $agent */
        foreach ($ws->agents() as $agent) {
            $agent->send(WS::Text, $message);
        }
    }

    /**
     * Writes a debug message to `STDOUT`.
     *
     * @param string $message
     */
    private function debug(string $message)
    {
        $date = date('Y-m-d H:i:s');
        $memory = round(memory_get_usage(true) / 1000 / 1000, 3) . ' MB';

        fwrite(STDOUT, $date . ' | ' . $memory . ' | ' . $message . "\n");
    }

    public function onStart(WS $ws)
    {
        $this->debug('WebSocket server started');
    }

    public function onError(WS $ws)
    {
        $this->debug(__METHOD__);

        if ($err = socket_last_error()) {
            $this->debug(socket_strerror($err));
            socket_clear_error();
        }

        if ($err = error_get_last()) {
            $this->debug($err['message']);
        }
    }

    public function onStop(WS $ws)
    {
        $this->debug('Shutting down');
    }

    public function onConnect(Agent $agent)
    {
        $this->debug('Agent ' . $agent->id() . ' connected');

        $this->sendMessageToAllClients($agent->server(), sprintf("Client with ID %s joined", $agent->id()));
    }

    public function onDisconnect(Agent $agent)
    {
        $this->debug('Agent ' . $agent->id() . ' disconnected');

        if ($err = socket_last_error()) {
            $this->debug(socket_strerror($err));
            socket_clear_error();
        }

        $this->sendMessageToAllClients($agent->server(), sprintf("Client with ID %s left", $agent->id()));
    }

    public function onIdle(Agent $agent)
    {
        $this->debug('Agent ' . $agent->id() . ' idles');
    }

    public function onReceive(Agent $agent, int $op, string $data)
    {
        // This example is only utilizing text frames for application-specific payload.
        if ($op != WS::Text) {
            $this->debug(sprintf('Agent %s sent a message with ignored opcode %s.', $agent->id(), $op));
            return;
        }

        $this->debug(sprintf('Agent %s sent a message: %s', $agent->id(), $data));

        /**
         * Forward received message to all clients.
         */
        $message = json_encode([
            'author' => $agent->id(),
            'message' => trim($data),
        ]);

        $this->debug('Forward message to all clients: ' . $message);
        $this->sendMessageToAllClients($agent->server(), $message);
    }

    public function onSend(Agent $agent, int $op, string $data)
    {
        $this->debug(sprintf('Agent %s will receive a message: %s', $agent->id(), $data));
    }
}
