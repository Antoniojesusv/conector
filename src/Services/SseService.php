<?php

namespace App\Services;

class SseService
{
    public function emitEventToClient($eventType, $callback)
    {
        session_write_close();
        ignore_user_abort(true);

        $oldMd5Hash = '';

        while (true) {
            if (connection_aborted()) {
                exit();
            }

            $this->streamHeaders();
            $data = $callback();
            $md5Hash = $this->createMd5Hash($data);

            if ($md5Hash != $oldMd5Hash) {
                $this->emitEvent($eventType, $data);
                $oldMd5Hash = $md5Hash;
            }

            $this->flushOutBuffer();
            sleep(1);
        }
    }

    private function createMd5Hash($data)
    {
        $encodedData = json_encode($data);
        return md5($encodedData);
    }

    private function emitEvent($eventType, $msg)
    {
        $data = json_encode($msg);

        echo "event: $eventType" . PHP_EOL;
        echo "data: $data" . PHP_EOL;
        echo PHP_EOL;
    }

    private function streamHeaders()
    {
        header("Content-Type: text/event-stream");
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header("Access-Control-Allow-Origin: *");
    }

    private function flushOutBuffer()
    {
        ob_flush();
        flush();
    }
}
