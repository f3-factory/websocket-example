# WebSocket Example Implementation

This repository contains a simple WebSocket-based chat example built with the [Fat-Free Framework](https://fatfreeframework.com) and HTML5.

## Contents

* [Features](#features)
* [Implementation](#implementation)
* [Run & Chat](#run--chat)
* [Example Output](#example-output)

## Features

-   Provide a WebSocket server
-   Log every `\CLI\WS` event
-   Notify clients that users joined or left the chat
-   Receive chat messages
-   Forward chat messages to all clients

## Implementation

The WebSocket server implementation consists of two files:

-   `/bin/websocket-server.php` configures and starts the chat backend

-   `/src/App/WebSocketServer.php` registers listeners and implements the application-specific features

## Run & Chat

1.  Install Fat-Free Framework

    ```bash
    composer install --no-dev
    ```

2.  Start the WebSocket server

    ```bash
    ./bin/websocket-server.php tcp://127.0.0.1:9001
    ```

3.  Start PHP's built-in server or the server of your choice
    and make the `/public` directory accessible via HTTP

    ```bash
    cd public && php -S 127.0.0.1:9000
    ```

4.  Visit the HTML5-based chat client, e.g. http://127.0.0.1:9000.
    The web client will automatically connect to the same host on port 9001
    (e.g. `ws://127.0.0.1:9001`, see `/public/index.html`).
    As soon as the connection is ready,
    it is possible to chat with other visitors.

## Example Output

The `\App\WebSocketServer` class listens to all `\CLI\WS` events and logs the results.

**WebSocket Server**

```
2020-04-04 20:17:26 | 2.097 MB | WebSocket server started
2020-04-04 20:17:37 | 2.097 MB | Agent 127.0.0.1:42478 connected
2020-04-04 20:17:41 | 2.097 MB | Agent 127.0.0.1:42480 connected
2020-04-04 20:17:41 | 2.097 MB | Agent 127.0.0.1:42478 will receive a message: Client with ID 127.0.0.1:42480 joined
2020-04-04 20:17:53 | 2.097 MB | Agent 127.0.0.1:42478 sent a message: Hello World
2020-04-04 20:17:53 | 2.097 MB | Forward message to all clients: {"author":"127.0.0.1:42478","message":"Hello World"}
2020-04-04 20:17:53 | 2.097 MB | Agent 127.0.0.1:42478 will receive a message: {"author":"127.0.0.1:42478","message":"Hello World"}
2020-04-04 20:17:53 | 2.097 MB | Agent 127.0.0.1:42480 will receive a message: {"author":"127.0.0.1:42478","message":"Hello World"}
2020-04-04 20:18:05 | 2.097 MB | Agent 127.0.0.1:42480 disconnected
2020-04-04 20:18:05 | 2.097 MB | Agent 127.0.0.1:42478 will receive a message: Client with ID 127.0.0.1:42480 left
2020-04-04 20:18:15 | 2.097 MB | Agent 127.0.0.1:42478 disconnected
2020-04-04 20:18:15 | 2.097 MB | Shutting down
```

**WebSocket Client**

```
Connect to ws://127.0.0.1:9001
WebSocket.onopen() called
Client with ID 127.0.0.1:42480 joined
{"author":"127.0.0.1:42478","message":"Hello World"}
Client with ID 127.0.0.1:42480 left
WebSocket.onclose() called
```
